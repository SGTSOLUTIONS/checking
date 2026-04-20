<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Advanced GeoJSON Editor with Local Storage</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@7.4.0/ol.css">
  <script src="https://cdn.jsdelivr.net/npm/ol@7.4.0/dist/ol.js"></script>
  <script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/idb@7/build/umd.js"></script>
  <style>
    :root {
      --primary-color: #2c5aa0;
      --secondary-color: #4caf50;
      --danger-color: #f44336;
      --warning-color: #ff9800;
      --light-gray: #f5f5f5;
      --medium-gray: #e0e0e0;
      --dark-gray: #757575;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    
    .header {
      background-color: var(--primary-color);
      color: white;
      padding: 10px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .header h1 {
      font-size: 1.5rem;
      font-weight: 500;
    }
    
    .header-controls {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .user-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background-color: white;
      color: var(--primary-color);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }
    
    .main-container {
      display: flex;
      flex: 1;
      overflow: hidden;
    }
    
    .sidebar {
      width: 350px;
      background-color: var(--light-gray);
      border-right: 1px solid var(--medium-gray);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    
    .sidebar-tabs {
      display: flex;
      border-bottom: 1px solid var(--medium-gray);
    }
    
    .sidebar-tab {
      flex: 1;
      padding: 12px;
      text-align: center;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.2s;
    }
    
    .sidebar-tab.active {
      background-color: white;
      border-bottom: 2px solid var(--primary-color);
      font-weight: 500;
    }
    
    .sidebar-content {
      flex: 1;
      overflow-y: auto;
      padding: 15px;
    }
    
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
    }
    
    .layer-list {
      list-style-type: none;
      margin-top: 10px;
    }
    
    .layer-item {
      padding: 10px;
      background-color: white;
      margin-bottom: 8px;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .layer-info {
      flex: 1;
    }
    
    .layer-name {
      font-weight: 500;
    }
    
    .layer-meta {
      font-size: 12px;
      color: var(--dark-gray);
      margin-top: 2px;
    }
    
    .layer-controls {
      display: flex;
      gap: 5px;
    }
    
    .layer-controls button {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--dark-gray);
      font-size: 16px;
    }
    
    .layer-controls button:hover {
      color: var(--primary-color);
    }
    
    .tools-panel {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-top: 15px;
    }
    
    .tool-btn {
      padding: 10px;
      background-color: white;
      border: 1px solid var(--medium-gray);
      border-radius: 4px;
      cursor: pointer;
      text-align: center;
      transition: all 0.2s;
    }
    
    .tool-btn:hover {
      background-color: var(--light-gray);
    }
    
    .tool-btn.active {
      background-color: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }
    
    .attributes-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    
    .attributes-table th, .attributes-table td {
      border: 1px solid var(--medium-gray);
      padding: 8px;
      text-align: left;
    }
    
    .attributes-table th {
      background-color: var(--light-gray);
    }
    
    .attributes-table input {
      width: 100%;
      padding: 4px;
      border: 1px solid var(--medium-gray);
      border-radius: 2px;
    }
    
    .map-container {
      flex: 1;
      position: relative;
    }
    
    #map {
      width: 100%;
      height: 100%;
    }
    
    .status-bar {
      padding: 8px 15px;
      background-color: var(--light-gray);
      border-top: 1px solid var(--medium-gray);
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      color: var(--dark-gray);
    }
    
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      width: 450px;
      max-width: 90%;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      max-height: 90vh;
      overflow-y: auto;
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .modal-header h3 {
      margin: 0;
    }
    
    .close-btn {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
    }
    
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid var(--medium-gray);
      border-radius: 4px;
    }
    
    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }
    
    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 500;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      color: white;
    }
    
    .btn-secondary {
      background-color: var(--medium-gray);
      color: #333;
    }
    
    .btn-danger {
      background-color: var(--danger-color);
      color: white;
    }
    
    .btn-warning {
      background-color: var(--warning-color);
      color: white;
    }
    
    .projects-list {
      margin-top: 15px;
    }
    
    .project-item {
      padding: 10px;
      background-color: white;
      margin-bottom: 8px;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .project-item:hover {
      background-color: var(--light-gray);
    }
    
    .project-name {
      font-weight: 500;
    }
    
    .project-meta {
      font-size: 12px;
      color: var(--dark-gray);
      margin-top: 2px;
    }
    
    .user-management {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    
    .user-select {
      padding: 5px 10px;
      border-radius: 4px;
      border: 1px solid var(--medium-gray);
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Advanced GeoJSON Editor</h1>
    <div class="header-controls">
      <div class="user-info">
        <div class="user-avatar" id="userAvatar">U</div>
        <span id="userName">User</span>
      </div>
      <div class="user-management">
        <select id="userSelect" class="user-select">
          <option value="user1">User 1</option>
          <option value="user2">User 2</option>
          <option value="user3">User 3</option>
        </select>
        <button id="userSettingsBtn" class="btn btn-secondary">Settings</button>
      </div>
      <input type="file" id="fileInput" accept=".geojson,.json,.zip" style="display: none;">
      <button id="uploadBtn" class="btn btn-primary">Upload Data</button>
      <button id="saveBtn" class="btn btn-primary" disabled>Save Project</button>
      <button id="exportBtn" class="btn btn-secondary">Export</button>
    </div>
  </div>
  
  <div class="main-container">
    <div class="sidebar">
      <div class="sidebar-tabs">
        <button class="sidebar-tab active" data-tab="projects">Projects</button>
        <button class="sidebar-tab" data-tab="layers">Layers</button>
        <button class="sidebar-tab" data-tab="tools">Tools</button>
        <button class="sidebar-tab" data-tab="attributes">Attributes</button>
      </div>
      
      <div class="sidebar-content">
        <div id="projects-tab" class="tab-content active">
          <h3>Projects</h3>
          <button id="newProjectBtn" class="btn btn-primary" style="width: 100%; margin-top: 10px;">New Project</button>
          <div class="projects-list" id="projectsList">
            <!-- Projects will be added here dynamically -->
          </div>
        </div>
        
        <div id="layers-tab" class="tab-content">
          <h3>Layers</h3>
          <ul class="layer-list" id="layerList">
            <!-- Layers will be added here dynamically -->
          </ul>
          <button id="addLayerBtn" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Add New Layer</button>
        </div>
        
        <div id="tools-tab" class="tab-content">
          <h3>Editing Tools</h3>
          <div class="tools-panel">
            <button class="tool-btn" data-tool="point">Add Point</button>
            <button class="tool-btn" data-tool="line">Add Line</button>
            <button class="tool-btn" data-tool="polygon">Add Polygon</button>
            <button class="tool-btn" data-tool="select">Select</button>
            <button class="tool-btn" data-tool="modify">Modify</button>
            <button class="tool-btn" data-tool="delete">Delete</button>
          </div>
          
          <div style="margin-top: 20px;">
            <h3>Snapping</h3>
            <label>
              <input type="checkbox" id="snappingToggle" checked> Enable Snapping
            </label>
          </div>
          
          <div style="margin-top: 20px;">
            <h3>Auto-Save</h3>
            <label>
              <input type="checkbox" id="autoSaveToggle"> Enable Auto-Save
            </label>
          </div>
        </div>
        
        <div id="attributes-tab" class="tab-content">
          <h3>Feature Attributes</h3>
          <div id="attributesContainer">
            <p>Select a feature to edit its attributes</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="map-container">
      <div id="map"></div>
    </div>
  </div>
  
  <div class="status-bar">
    <div id="coordinates">Coordinates: </div>
    <div id="status">Ready</div>
    <div id="projectInfo">No project loaded</div>
  </div>
  
  <!-- Add Layer Modal -->
  <div id="addLayerModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Add New Layer</h3>
        <button class="close-btn">&times;</button>
      </div>
      <div class="form-group">
        <label for="layerName">Layer Name</label>
        <input type="text" id="layerName" placeholder="Enter layer name">
      </div>
      <div class="form-group">
        <label for="layerType">Geometry Type</label>
        <select id="layerType">
          <option value="Point">Point</option>
          <option value="LineString">Line</option>
          <option value="Polygon">Polygon</option>
          <option value="Mixed">Mixed</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="cancelLayerBtn">Cancel</button>
        <button class="btn btn-primary" id="createLayerBtn">Create</button>
      </div>
    </div>
  </div>
  
  <!-- New Project Modal -->
  <div id="newProjectModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Create New Project</h3>
        <button class="close-btn">&times;</button>
      </div>
      <div class="form-group">
        <label for="projectName">Project Name</label>
        <input type="text" id="projectName" placeholder="Enter project name">
      </div>
      <div class="form-group">
        <label for="projectDescription">Description</label>
        <textarea id="projectDescription" placeholder="Enter project description"></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="cancelProjectBtn">Cancel</button>
        <button class="btn btn-primary" id="createProjectBtn">Create</button>
      </div>
    </div>
  </div>
  
  <!-- Export Modal -->
  <div id="exportModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Export Data</h3>
        <button class="close-btn">&times;</button>
      </div>
      <div class="form-group">
        <label for="exportFormat">Format</label>
        <select id="exportFormat">
          <option value="geojson">GeoJSON</option>
          <option value="kml">KML</option>
          <option value="gpx">GPX</option>
        </select>
      </div>
      <div class="form-group">
        <label for="exportLayers">Layers to Export</label>
        <div id="exportLayers">
          <!-- Layers checkboxes will be added here -->
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="cancelExportBtn">Cancel</button>
        <button class="btn btn-primary" id="confirmExportBtn">Export</button>
      </div>
    </div>
  </div>
  
  <!-- User Settings Modal -->
  <div id="userSettingsModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>User Settings</h3>
        <button class="close-btn">&times;</button>
      </div>
      <div class="form-group">
        <label for="userNameInput">User Name</label>
        <input type="text" id="userNameInput" placeholder="Enter your name">
      </div>
      <div class="form-group">
        <label for="userFolder">Data Folder Name</label>
        <input type="text" id="userFolder" placeholder="Enter folder name">
      </div>
      <div class="form-group">
        <label for="backupFrequency">Auto-Save Frequency (minutes)</label>
        <input type="number" id="backupFrequency" min="1" max="60" value="5">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="cancelSettingsBtn">Cancel</button>
        <button class="btn btn-primary" id="saveSettingsBtn">Save Settings</button>
      </div>
    </div>
  </div>

  <script>
    // Initialize Map
    const map = new ol.Map({
      target: 'map',
      layers: [
        new ol.layer.Tile({
          source: new ol.source.OSM()
        })
      ],
      view: new ol.View({
        center: ol.proj.fromLonLat([78.1, 10.3]),
        zoom: 10
      })
    });

    // Application State
    const appState = {
      currentUser: 'user1',
      currentProject: null,
      projects: [],
      layers: [],
      activeLayer: null,
      activeTool: null,
      selectedFeature: null,
      projectModified: false,
      snapInteraction: null,
      autoSaveEnabled: false,
      autoSaveInterval: null,
      db: null
    };

    // DOM Elements
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const saveBtn = document.getElementById('saveBtn');
    const exportBtn = document.getElementById('exportBtn');
    const addLayerBtn = document.getElementById('addLayerBtn');
    const newProjectBtn = document.getElementById('newProjectBtn');
    const userSelect = document.getElementById('userSelect');
    const userSettingsBtn = document.getElementById('userSettingsBtn');
    const layerList = document.getElementById('layerList');
    const projectsList = document.getElementById('projectsList');
    const attributesContainer = document.getElementById('attributesContainer');
    const snappingToggle = document.getElementById('snappingToggle');
    const autoSaveToggle = document.getElementById('autoSaveToggle');
    const addLayerModal = document.getElementById('addLayerModal');
    const newProjectModal = document.getElementById('newProjectModal');
    const exportModal = document.getElementById('exportModal');
    const userSettingsModal = document.getElementById('userSettingsModal');

    // Initialize the application
    initializeApp();

    async function initializeApp() {
      // Initialize IndexedDB
      await initializeDatabase();
      
      // Load user settings
      await loadUserSettings();
      
      // Initialize UI
      initializeEventListeners();
      setupMapInteractions();
      
      // Load projects for current user
      await loadUserProjects();
    }

    async function initializeDatabase() {
      // Use IndexedDB for local storage
      appState.db = await idb.openDB('GeoJSONEditor', 1, {
        upgrade(db) {
          // Create object stores for users, projects, and layers
          if (!db.objectStoreNames.contains('users')) {
            const userStore = db.createObjectStore('users', { keyPath: 'id' });
            userStore.createIndex('name', 'name', { unique: false });
          }
          
          if (!db.objectStoreNames.contains('projects')) {
            const projectStore = db.createObjectStore('projects', { keyPath: 'id' });
            projectStore.createIndex('userId', 'userId', { unique: false });
            projectStore.createIndex('name', 'name', { unique: false });
          }
          
          if (!db.objectStoreNames.contains('layers')) {
            const layerStore = db.createObjectStore('layers', { keyPath: 'id' });
            layerStore.createIndex('projectId', 'projectId', { unique: false });
          }
        }
      });
      
      // Initialize default users if they don't exist
      const defaultUsers = [
        { id: 'user1', name: 'User 1', folder: 'user1_data', autoSave: 5 },
        { id: 'user2', name: 'User 2', folder: 'user2_data', autoSave: 5 },
        { id: 'user3', name: 'User 3', folder: 'user3_data', autoSave: 5 }
      ];
      
      for (const user of defaultUsers) {
        const existingUser = await appState.db.get('users', user.id);
        if (!existingUser) {
          await appState.db.put('users', user);
        }
      }
    }

    async function loadUserSettings() {
      const user = await appState.db.get('users', appState.currentUser);
      if (user) {
        document.getElementById('userName').textContent = user.name;
        document.getElementById('userAvatar').textContent = user.name.charAt(0).toUpperCase();
        document.getElementById('userNameInput').value = user.name;
        document.getElementById('userFolder').value = user.folder;
        document.getElementById('backupFrequency').value = user.autoSave;
        
        // Set auto-save toggle based on user preference
        autoSaveToggle.checked = user.autoSave > 0;
        appState.autoSaveEnabled = user.autoSave > 0;
        
        if (appState.autoSaveEnabled) {
          startAutoSave(user.autoSave);
        }
      }
    }

    async function loadUserProjects() {
      // Get projects for current user from IndexedDB
      const projects = await appState.db.getAllFromIndex('projects', 'userId', appState.currentUser);
      appState.projects = projects;
      
      // Update UI
      updateProjectsList();
    }

    function initializeEventListeners() {
      // Tab switching
      document.querySelectorAll('.sidebar-tab').forEach(tab => {
        tab.addEventListener('click', () => {
          document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
          document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
          
          tab.classList.add('active');
          document.getElementById(`${tab.dataset.tab}-tab`).classList.add('active');
        });
      });

      // Tool buttons
      document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          if (btn.classList.contains('active')) {
            btn.classList.remove('active');
            setActiveTool(null);
          } else {
            document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            setActiveTool(btn.dataset.tool);
          }
        });
      });

      // Upload button
      uploadBtn.addEventListener('click', () => fileInput.click());
      fileInput.addEventListener('change', handleFileUpload);

      // Layer management
      addLayerBtn.addEventListener('click', () => addLayerModal.style.display = 'flex');
      document.querySelectorAll('.close-btn, #cancelLayerBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          addLayerModal.style.display = 'none';
          exportModal.style.display = 'none';
          newProjectModal.style.display = 'none';
          userSettingsModal.style.display = 'none';
        });
      });
      document.getElementById('createLayerBtn').addEventListener('click', createNewLayer);

      // Project management
      newProjectBtn.addEventListener('click', () => newProjectModal.style.display = 'flex');
      document.getElementById('createProjectBtn').addEventListener('click', createNewProject);
      document.getElementById('cancelProjectBtn').addEventListener('click', () => newProjectModal.style.display = 'none');

      // User management
      userSelect.addEventListener('change', switchUser);
      userSettingsBtn.addEventListener('click', () => userSettingsModal.style.display = 'flex');
      document.getElementById('saveSettingsBtn').addEventListener('click', saveUserSettings);
      document.getElementById('cancelSettingsBtn').addEventListener('click', () => userSettingsModal.style.display = 'none');

      // Save and export
      saveBtn.addEventListener('click', saveProject);
      exportBtn.addEventListener('click', () => exportModal.style.display = 'flex');
      document.getElementById('confirmExportBtn').addEventListener('click', exportData);

      // Snapping toggle
      snappingToggle.addEventListener('change', toggleSnapping);
      
      // Auto-save toggle
      autoSaveToggle.addEventListener('change', toggleAutoSave);
    }

    async function switchUser() {
      const newUser = userSelect.value;
      if (newUser !== appState.currentUser) {
        // Save current project if modified
        if (appState.projectModified && appState.currentProject) {
          if (confirm('You have unsaved changes. Save current project before switching users?')) {
            await saveProjectToDB();
          }
        }
        
        // Clear current state
        clearCurrentProject();
        
        // Switch user
        appState.currentUser = newUser;
        await loadUserSettings();
        await loadUserProjects();
        
        document.getElementById('status').textContent = `Switched to ${newUser}`;
      }
    }

    function clearCurrentProject() {
      // Remove all layers from map
      appState.layers.forEach(layer => {
        map.removeLayer(layer.olLayer);
      });
      
      // Reset application state
      appState.layers = [];
      appState.activeLayer = null;
      appState.currentProject = null;
      appState.projectModified = false;
      
      // Update UI
      updateLayerList();
      updateProjectsList();
      saveBtn.disabled = true;
      document.getElementById('projectInfo').textContent = 'No project loaded';
    }

    function setupMapInteractions() {
      // Pointer move to show coordinates
      map.on('pointermove', (e) => {
        const coords = ol.proj.toLonLat(e.coordinate);
        document.getElementById('coordinates').textContent = 
          `Coordinates: ${coords[0].toFixed(5)}, ${coords[1].toFixed(5)}`;
      });

      // Feature selection
      map.on('click', (e) => {
        if (appState.activeTool !== 'select') return;
        
        map.forEachFeatureAtPixel(e.pixel, (feature) => {
          if (appState.selectedFeature) {
            // Reset previous selection style
            appState.selectedFeature.setStyle(null);
          }
          
          appState.selectedFeature = feature;
          feature.setStyle(new ol.style.Style({
            stroke: new ol.style.Stroke({ color: 'yellow', width: 3 }),
            fill: new ol.style.Fill({ color: 'rgba(255, 255, 0, 0.2)' }),
            image: new ol.style.Circle({
              radius: 7,
              fill: new ol.style.Fill({ color: 'yellow' }),
              stroke: new ol.style.Stroke({ color: 'black', width: 2 })
            })
          }));
          
          displayAttributes(feature);
          return true;
        });
      });
    }

    function handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      const fileName = file.name.toLowerCase();
      const reader = new FileReader();

      if (fileName.endsWith('.geojson') || fileName.endsWith('.json')) {
        reader.onload = (e) => {
          const geojson = JSON.parse(e.target.result);
          addDataAsLayer(geojson, file.name.replace(/\.[^/.]+$/, ""));
        };
        reader.readAsText(file);
      } else if (fileName.endsWith('.zip')) {
        shp(file).then(geojson => {
          addDataAsLayer(geojson, file.name.replace(/\.[^/.]+$/, ""));
        });
      } else {
        alert('Please upload a .geojson or .zip (shapefile) file.');
      }
    }

    function addDataAsLayer(geojson, layerName) {
      // Make sure we have an active project
      if (!appState.currentProject) {
        alert('Please create or load a project first.');
        return;
      }

      const vectorSource = new ol.source.Vector({
        features: new ol.format.GeoJSON().readFeatures(geojson, {
          featureProjection: 'EPSG:3857'
        })
      });

      const vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: createLayerStyle()
      });

      map.addLayer(vectorLayer);

      // Add to application state
      const layerObj = {
        id: generateId(),
        name: layerName,
        olLayer: vectorLayer,
        source: vectorSource,
        type: getGeometryType(vectorSource),
        visible: true,
        projectId: appState.currentProject.id
      };

      appState.layers.push(layerObj);
      updateLayerList();
      setActiveLayer(layerObj);
      markProjectModified();

      // Fit map to layer extent
      const extent = vectorSource.getExtent();
      if (extent && extent[0] !== Infinity) {
        map.getView().fit(extent, { padding: [20, 20, 20, 20] });
      }
    }

    async function createNewProject() {
      const projectName = document.getElementById('projectName').value;
      const projectDescription = document.getElementById('projectDescription').value;
      
      if (!projectName) {
        alert('Please enter a project name');
        return;
      }

      const projectId = generateId();
      const project = {
        id: projectId,
        name: projectName,
        description: projectDescription,
        userId: appState.currentUser,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      };

      // Save to IndexedDB
      await appState.db.put('projects', project);
      
      // Add to application state
      appState.projects.push(project);
      appState.currentProject = project;
      
      // Update UI
      updateProjectsList();
      newProjectModal.style.display = 'none';
      document.getElementById('projectInfo').textContent = `Project: ${projectName}`;
      
      // Clear form
      document.getElementById('projectName').value = '';
      document.getElementById('projectDescription').value = '';
      
      document.getElementById('status').textContent = `Project "${projectName}" created`;
    }

    async function loadProject(projectId) {
      // Clear current project
      clearCurrentProject();
      
      // Load project from IndexedDB
      const project = await appState.db.get('projects', projectId);
      if (!project) {
        alert('Project not found');
        return;
      }
      
      appState.currentProject = project;
      
      // Load layers for this project
      const layers = await appState.db.getAllFromIndex('layers', 'projectId', projectId);
      
      // Add layers to map
      for (const layerData of layers) {
        const vectorSource = new ol.source.Vector({
          features: new ol.format.GeoJSON().readFeatures(layerData.geojson, {
            featureProjection: 'EPSG:3857'
          })
        });

        const vectorLayer = new ol.layer.Vector({
          source: vectorSource,
          style: createLayerStyle()
        });

        map.addLayer(vectorLayer);

        const layerObj = {
          id: layerData.id,
          name: layerData.name,
          olLayer: vectorLayer,
          source: vectorSource,
          type: layerData.type,
          visible: true,
          projectId: projectId
        };

        appState.layers.push(layerObj);
      }
      
      // Update UI
      updateLayerList();
      document.getElementById('projectInfo').textContent = `Project: ${project.name}`;
      document.getElementById('status').textContent = `Project "${project.name}" loaded`;
      
      // Switch to layers tab
      document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      document.querySelector('[data-tab="layers"]').classList.add('active');
      document.getElementById('layers-tab').classList.add('active');
    }

    function createNewLayer() {
      const layerName = document.getElementById('layerName').value;
      const layerType = document.getElementById('layerType').value;
      
      if (!layerName) {
        alert('Please enter a layer name');
        return;
      }

      // Make sure we have an active project
      if (!appState.currentProject) {
        alert('Please create or load a project first.');
        return;
      }

      const vectorSource = new ol.source.Vector();
      const vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: createLayerStyle()
      });

      map.addLayer(vectorLayer);

      const layerObj = {
        id: generateId(),
        name: layerName,
        olLayer: vectorLayer,
        source: vectorSource,
        type: layerType,
        visible: true,
        projectId: appState.currentProject.id
      };

      appState.layers.push(layerObj);
      updateLayerList();
      setActiveLayer(layerObj);
      addLayerModal.style.display = 'none';
      markProjectModified();

      // Clear form
      document.getElementById('layerName').value = '';
    }

    function setActiveLayer(layer) {
      appState.activeLayer = layer;
      updateLayerList();
    }

    function updateLayerList() {
      layerList.innerHTML = '';
      
      if (appState.layers.length === 0) {
        layerList.innerHTML = '<li class="layer-item">No layers in current project</li>';
        return;
      }
      
      appState.layers.forEach(layer => {
        const featureCount = layer.source.getFeatures().length;
        const li = document.createElement('li');
        li.className = 'layer-item';
        li.innerHTML = `
          <div class="layer-info">
            <div class="layer-name">${layer.name}</div>
            <div class="layer-meta">${layer.type} • ${featureCount} features</div>
          </div>
          <div class="layer-controls">
            <button class="visibility-toggle" data-id="${layer.id}">${layer.visible ? '👁️' : '👁️‍🗨️'}</button>
            <button class="edit-layer" data-id="${layer.id}">✏️</button>
            <button class="delete-layer" data-id="${layer.id}">🗑️</button>
          </div>
        `;
        
        // Set active layer style
        if (appState.activeLayer && appState.activeLayer.id === layer.id) {
          li.style.borderLeft = `4px solid ${getLayerColor(layer)}`;
        }
        
        layerList.appendChild(li);
      });

      // Add event listeners to layer controls
      document.querySelectorAll('.visibility-toggle').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const layerId = e.target.dataset.id;
          const layer = appState.layers.find(l => l.id === layerId);
          if (layer) {
            layer.visible = !layer.visible;
            layer.olLayer.setVisible(layer.visible);
            e.target.textContent = layer.visible ? '👁️' : '👁️‍🗨️';
            markProjectModified();
          }
        });
      });

      document.querySelectorAll('.edit-layer').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const layerId = e.target.dataset.id;
          const layer = appState.layers.find(l => l.id === layerId);
          if (layer) {
            setActiveLayer(layer);
          }
        });
      });

      document.querySelectorAll('.delete-layer').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const layerId = e.target.dataset.id;
          const layer = appState.layers.find(l => l.id === layerId);
          if (layer && confirm(`Are you sure you want to delete layer "${layer.name}"?`)) {
            map.removeLayer(layer.olLayer);
            appState.layers = appState.layers.filter(l => l.id !== layerId);
            updateLayerList();
            markProjectModified();
          }
        });
      });
    }

    function updateProjectsList() {
      projectsList.innerHTML = '';
      
      if (appState.projects.length === 0) {
        projectsList.innerHTML = '<div class="project-item">No projects found</div>';
        return;
      }
      
      appState.projects.forEach(project => {
        const div = document.createElement('div');
        div.className = 'project-item';
        div.innerHTML = `
          <div class="project-name">${project.name}</div>
          <div class="project-meta">Created: ${new Date(project.createdAt).toLocaleDateString()}</div>
        `;
        
        div.addEventListener('click', () => {
          loadProject(project.id);
        });
        
        projectsList.appendChild(div);
      });
    }

    function setActiveTool(tool) {
      appState.activeTool = tool;
      
      // Remove previous interactions
      map.getInteractions().forEach(interaction => {
        if (interaction.get('editing')) {
          map.removeInteraction(interaction);
        }
      });

      if (!tool || !appState.activeLayer) return;

      let newInteraction;
      
      switch(tool) {
        case 'point':
          newInteraction = new ol.interaction.Draw({
            source: appState.activeLayer.source,
            type: 'Point'
          });
          break;
        case 'line':
          newInteraction = new ol.interaction.Draw({
            source: appState.activeLayer.source,
            type: 'LineString'
          });
          break;
        case 'polygon':
          newInteraction = new ol.interaction.Draw({
            source: appState.activeLayer.source,
            type: 'Polygon'
          });
          break;
        case 'modify':
          newInteraction = new ol.interaction.Modify({
            source: appState.activeLayer.source
          });
          break;
        case 'delete':
          // Delete on click
          map.once('click', (e) => {
            map.forEachFeatureAtPixel(e.pixel, (feature) => {
              appState.activeLayer.source.removeFeature(feature);
              markProjectModified();
              return true;
            });
          });
          setActiveTool(null);
          document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
          return;
      }

      if (newInteraction) {
        newInteraction.set('editing', true);
        map.addInteraction(newInteraction);
        
        newInteraction.on('drawend', () => {
          markProjectModified();
        });
      }
    }

    function displayAttributes(feature) {
      const properties = feature.getProperties();
      delete properties.geometry; // Remove geometry from attributes display
      
      let html = '<table class="attributes-table"><thead><tr><th>Property</th><th>Value</th></tr></thead><tbody>';
      
      Object.entries(properties).forEach(([key, value]) => {
        html += `<tr>
          <td>${key}</td>
          <td><input type="text" value="${value || ''}" data-key="${key}"></td>
        </tr>`;
      });
      
      html += '</tbody></table>';
      html += '<div style="margin-top: 10px;"><button id="saveAttributes" class="btn btn-primary">Save Changes</button></div>';
      
      attributesContainer.innerHTML = html;
      
      document.getElementById('saveAttributes').addEventListener('click', () => {
        const inputs = attributesContainer.querySelectorAll('input');
        inputs.forEach(input => {
          feature.set(input.dataset.key, input.value);
        });
        markProjectModified();
        document.getElementById('status').textContent = 'Attributes saved';
      });
    }

    function toggleSnapping() {
      if (snappingToggle.checked) {
        if (!appState.snapInteraction) {
          appState.snapInteraction = new ol.interaction.Snap({
            source: appState.layers.map(layer => layer.source)
          });
        }
        map.addInteraction(appState.snapInteraction);
      } else {
        if (appState.snapInteraction) {
          map.removeInteraction(appState.snapInteraction);
        }
      }
    }

    function toggleAutoSave() {
      appState.autoSaveEnabled = autoSaveToggle.checked;
      
      if (appState.autoSaveEnabled) {
        const user = appState.db.get('users', appState.currentUser);
        startAutoSave(user.autoSave || 5);
        document.getElementById('status').textContent = 'Auto-save enabled';
      } else {
        stopAutoSave();
        document.getElementById('status').textContent = 'Auto-save disabled';
      }
    }

    function startAutoSave(intervalMinutes) {
      stopAutoSave(); // Clear any existing interval
      
      appState.autoSaveInterval = setInterval(() => {
        if (appState.projectModified && appState.currentProject) {
          saveProjectToDB();
          document.getElementById('status').textContent = 'Auto-saved project';
        }
      }, intervalMinutes * 60 * 1000);
    }

    function stopAutoSave() {
      if (appState.autoSaveInterval) {
        clearInterval(appState.autoSaveInterval);
        appState.autoSaveInterval = null;
      }
    }

    async function saveProject() {
      await saveProjectToDB();
      document.getElementById('status').textContent = 'Project saved';
    }

    async function saveProjectToDB() {
      if (!appState.currentProject) {
        alert('No project to save');
        return;
      }

      // Update project timestamp
      appState.currentProject.updatedAt = new Date().toISOString();
      await appState.db.put('projects', appState.currentProject);
      
      // Save all layers
      for (const layer of appState.layers) {
        const geojson = new ol.format.GeoJSON().writeFeatures(
          layer.source.getFeatures(), 
          { featureProjection: 'EPSG:3857', dataProjection: 'EPSG:4326' }
        );
        
        const layerData = {
          id: layer.id,
          name: layer.name,
          type: layer.type,
          projectId: appState.currentProject.id,
          geojson: JSON.parse(geojson)
        };
        
        await appState.db.put('layers', layerData);
      }
      
      appState.projectModified = false;
      saveBtn.disabled = true;
    }

    async function saveUserSettings() {
      const userName = document.getElementById('userNameInput').value;
      const userFolder = document.getElementById('userFolder').value;
      const backupFrequency = parseInt(document.getElementById('backupFrequency').value);
      
      if (!userName || !userFolder) {
        alert('Please fill in all fields');
        return;
      }

      const user = {
        id: appState.currentUser,
        name: userName,
        folder: userFolder,
        autoSave: backupFrequency
      };
      
      await appState.db.put('users', user);
      
      // Update UI
      document.getElementById('userName').textContent = userName;
      document.getElementById('userAvatar').textContent = userName.charAt(0).toUpperCase();
      
      // Update auto-save if enabled
      if (appState.autoSaveEnabled) {
        startAutoSave(backupFrequency);
      }
      
      userSettingsModal.style.display = 'none';
      document.getElementById('status').textContent = 'User settings saved';
    }

    function exportData() {
      const format = document.getElementById('exportFormat').value;
      const layersToExport = Array.from(document.querySelectorAll('#exportLayers input:checked'))
        .map(cb => cb.value);
      
      let exportData;
      
      if (format === 'geojson') {
        // Combine selected layers into one GeoJSON
        const allFeatures = [];
        appState.layers
          .filter(layer => layersToExport.includes(layer.id))
          .forEach(layer => {
            allFeatures.push(...layer.source.getFeatures());
          });
        
        exportData = new ol.format.GeoJSON().writeFeatures(allFeatures, {
          featureProjection: 'EPSG:3857',
          dataProjection: 'EPSG:4326'
        });
      } else if (format === 'kml') {
        // Similar approach for KML
        const allFeatures = [];
        appState.layers
          .filter(layer => layersToExport.includes(layer.id))
          .forEach(layer => {
            allFeatures.push(...layer.source.getFeatures());
          });
        
        exportData = new ol.format.KML().writeFeatures(allFeatures, {
          featureProjection: 'EPSG:3857',
          dataProjection: 'EPSG:4326'
        });
      } else if (format === 'gpx') {
        // For GPX, we only export point features
        const pointFeatures = [];
        appState.layers
          .filter(layer => layersToExport.includes(layer.id))
          .forEach(layer => {
            layer.source.getFeatures().forEach(feature => {
              if (feature.getGeometry().getType() === 'Point') {
                pointFeatures.push(feature);
              }
            });
          });
        
        exportData = new ol.format.GPX().writeFeatures(pointFeatures, {
          featureProjection: 'EPSG:3857',
          dataProjection: 'EPSG:4326'
        });
      }
      
      const blob = new Blob([exportData], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `export.${format}`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
      
      exportModal.style.display = 'none';
      document.getElementById('status').textContent = 'Data exported';
    }

    function markProjectModified() {
      if (!appState.projectModified) {
        appState.projectModified = true;
        saveBtn.disabled = false;
        document.getElementById('status').textContent = 'Modified';
      }
    }

    // Helper functions
    function generateId() {
      return Date.now().toString(36) + Math.random().toString(36).substr(2, 9);
    }

    function getGeometryType(source) {
      const features = source.getFeatures();
      if (features.length === 0) return 'Empty';
      
      const types = new Set();
      features.forEach(feature => {
        const geom = feature.getGeometry();
        if (geom) types.add(geom.getType());
      });
      
      return types.size === 1 ? Array.from(types)[0] : 'Mixed';
    }

    function createLayerStyle() {
      return new ol.style.Style({
        stroke: new ol.style.Stroke({ color: 'blue', width: 2 }),
        fill: new ol.style.Fill({ color: 'rgba(0, 0, 255, 0.1)' }),
        image: new ol.style.Circle({
          radius: 5,
          fill: new ol.style.Fill({ color: 'red' })
        })
      });
    }

    function getLayerColor(layer) {
      const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff'];
      const index = appState.layers.findIndex(l => l.id === layer.id);
      return colors[index % colors.length];
    }

    // Update export layers list when export modal opens
    exportModal.addEventListener('click', () => {
      const exportLayersContainer = document.getElementById('exportLayers');
      exportLayersContainer.innerHTML = '';
      
      appState.layers.forEach(layer => {
        const div = document.createElement('div');
        div.innerHTML = `
          <label>
            <input type="checkbox" value="${layer.id}" checked> ${layer.name} (${layer.type})
          </label>
        `;
        exportLayersContainer.appendChild(div);
      });
    });
  </script>
</body>
</html>