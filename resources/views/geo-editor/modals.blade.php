<!-- New Project Modal -->
<div class="modal fade" id="newProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newProjectForm">
                    @csrf
                    <div class="mb-3">
                        <label for="projectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control" id="projectName" required>
                    </div>
                    <div class="mb-3">
                        <label for="projectDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="projectDescription" rows="3"></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="projectIsPublic">
                        <label class="form-check-label" for="projectIsPublic">
                            Make project public
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createProjectBtn">Create Project</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Layer Modal -->
<div class="modal fade" id="addLayerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Layer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addLayerForm">
                    @csrf
                    <div class="mb-3">
                        <label for="layerName" class="form-label">Layer Name</label>
                        <input type="text" class="form-control" id="layerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="layerType" class="form-label">Geometry Type</label>
                        <select class="form-select" id="layerType">
                            <option value="Point">Point</option>
                            <option value="LineString">Line</option>
                            <option value="Polygon">Polygon</option>
                            <option value="Mixed">Mixed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createLayerBtn">Create Layer</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="exportFormat" class="form-label">Format</label>
                    <select class="form-select" id="exportFormat">
                        <option value="geojson">GeoJSON</option>
                        <option value="kml">KML</option>
                        <option value="gpx">GPX</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Layers to Export</label>
                    <div id="exportLayers">
                        <!-- Layers checkboxes will be added here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExportBtn">Export</button>
            </div>
        </div>
    </div>
</div>