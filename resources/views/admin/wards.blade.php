@extends('layouts.admin-layout')
@section('title', 'Ward Management')

@section('content')
    <div class="container-fluid py-3">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold text-primary"><i class="fas fa-map-marked-alt me-2"></i>Ward Management</h1>
                <p class="text-muted mb-0" id="corporationInfo">
                    <i class="fas fa-city me-1"></i>{{ $corporation->name ?? 'N/A' }} - {{ $corporation->code ?? 'N/A' }}
                </p>
            </div>
            <div>
                <a href="{{ url('admin/corporation-data') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Corporations
                </a>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#addWardModal">
                    <i class="fas fa-plus me-1"></i> Add Ward
                </button>
            </div>
        </div>

        <!-- Ward Cards Container -->
        <div class="row g-4" id="wardDataContainer">
            <div class="col-12 text-center text-muted py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading ward data...</p>
            </div>
        </div>
    </div>

    <!-- Add Ward Modal -->
    <div class="modal fade" id="addWardModal" tabindex="-1" aria-labelledby="addWardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addWardModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Ward
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="wardForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="corporation_id" value="{{ $corporationId }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Corporation</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $corporation->name }} ({{ $corporation->code }})" readonly>
                                <small class="form-text text-muted">Corporation ID: {{ $corporationId }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Ward No</label>
                                <input type="text" class="form-control" name="ward_no" id="ward_no"
                                    placeholder="Enter Ward Number" required>
                                <div class="invalid-feedback">Please enter ward number.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Zone</label>
                                <select class="form-select" name="zone" id="zone" required>
                                    <option value="">Select Zone</option>
                                    <option value="East">East</option>
                                    <option value="West">West</option>
                                    <option value="North">North</option>
                                    <option value="South">South</option>
                                    <option value="Central">Central</option>
                                </select>
                                <div class="invalid-feedback">Please select zone.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>

                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-arrows-alt me-2"></i>Extent
                                    Coordinates</h6>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Left</label>
                                <input type="text" class="form-control" name="extent_left" id="extent_left"
                                    placeholder="Left coordinate">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Right</label>
                                <input type="text" class="form-control" name="extent_right" id="extent_right"
                                    placeholder="Right coordinate">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Top</label>
                                <input type="text" class="form-control" name="extent_top" id="extent_top"
                                    placeholder="Top coordinate">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Bottom</label>
                                <input type="text" class="form-control" name="extent_bottom" id="extent_bottom"
                                    placeholder="Bottom coordinate">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-file-upload me-2"></i>File Uploads
                                </h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Drone Image</label>
                                <input type="file" class="form-control" name="drone_image" accept="image/*">
                                <div class="form-text">Formats: JPG, PNG, GIF. Max: 2MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Boundary File</label>
                                <input type="file" class="form-control" name="boundary"
                                    accept=".geojson,application/json">
                                <div class="form-text">GeoJSON format. Max: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Polygon File</label>
                                <input type="file" class="form-control" name="polygon"
                                    accept=".geojson,application/json">
                                <div class="form-text">GeoJSON format. Max: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Line File</label>
                                <input type="file" class="form-control" name="line"
                                    accept=".geojson,application/json">
                                <div class="form-text">GeoJSON format. Max: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="modal-footer mt-4 px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Save Ward
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Ward Modal -->
    <div class="modal fade" id="updateWardModal" tabindex="-1" aria-labelledby="updateWardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="updateWardModalLabel">
                        <i class="fas fa-edit me-2"></i>Update Ward
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateWardForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="update_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Corporation</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $corporation->name }} ({{ $corporation->code }})" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Ward No</label>
                                <input type="text" class="form-control" name="ward_no" id="update_ward_no" required>
                                <div class="invalid-feedback">Please enter ward number.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Zone</label>
                                <select class="form-select" name="zone" id="update_zone" required>
                                    <option value="">Select Zone</option>
                                    <option value="East">East</option>
                                    <option value="West">West</option>
                                    <option value="North">North</option>
                                    <option value="South">South</option>
                                    <option value="Central">Central</option>
                                </select>
                                <div class="invalid-feedback">Please select zone.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Status</label>
                                <select class="form-select" name="status" id="update_status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>

                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-arrows-alt me-2"></i>Extent
                                    Coordinates</h6>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Left</label>
                                <input type="text" class="form-control" name="extent_left" id="update_extent_left">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Right</label>
                                <input type="text" class="form-control" name="extent_right" id="update_extent_right">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Top</label>
                                <input type="text" class="form-control" name="extent_top" id="update_extent_top">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Extent Bottom</label>
                                <input type="text" class="form-control" name="extent_bottom"
                                    id="update_extent_bottom">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-file-upload me-2"></i>File Uploads
                                </h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Drone Image</label>
                                <input type="file" class="form-control" name="drone_image" accept="image/*">
                                <div id="updateDroneImagePreview" class="mt-2"></div>
                                <div class="form-text">Leave empty to keep current image</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Boundary File</label>
                                <input type="file" class="form-control" name="boundary"
                                    accept=".geojson,application/json">
                                <div class="form-text">Leave empty to keep current boundary</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Polygon File</label>
                                <input type="file" class="form-control" name="polygon"
                                    accept=".geojson,application/json">
                                <div class="form-text">Leave empty to keep current polygon</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Line File</label>
                                <input type="file" class="form-control" name="line"
                                    accept=".geojson,application/json">
                                <div class="form-text">Leave empty to keep current line</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="modal-footer mt-4 px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4" id="updateSubmitBtn">
                                <i class="fas fa-save me-1"></i> Update Ward
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete ward <strong id="wardNameToDelete"></strong>?</p>
                    <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i> Delete Ward
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-circle me-2 toast-icon"></i>
                <strong class="me-auto toast-title">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body toast-message"></div>
        </div>
    </div>

@endsection

@section('css')
    <style>
        .ward-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            height: 100%;
        }

        .ward-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            border-color: #007bff;
        }

        .ward-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #007bff;
            background: #fff;
        }

        .ward-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .ward-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .ward-actions {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .form-text {
            font-size: 0.75rem;
        }

        .toast.success .toast-icon {
            color: #198754;
        }

        .toast.error .toast-icon {
            color: #dc3545;
        }

        /* Roads Select Dropdown Styling */
        .roads-select {
            width: 100%;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            background-color: #fff;
            cursor: pointer;
        }

        .roads-select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .selected-road-display {
            margin-top: 8px;
            padding: 6px 10px;
            background-color: #e7f1ff;
            border-left: 3px solid #007bff;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #004085;
            display: none;
        }

        .selected-road-display i {
            margin-right: 5px;
        }

        /* Form select styling */
        .form-select {
            display: block;
            width: 100%;
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let currentDeleteId = null;

            // Load ward data on page load
            window.loadWardData = function() {
                $.ajax({
                    url: `{{ url('admin/wards') }}/{{ $corporationId }}/data`,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#wardDataContainer').html(`
                        <div class="col-12 text-center text-muted py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading ward data...</p>
                        </div>
                    `);
                    },
                    success: function(response) {
                        if (response.success) {
                            renderWardCards(response.wards);
                        } else {
                            showToast('error', 'Error!', 'Failed to load ward data.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading ward data:', error);
                        $('#wardDataContainer').html(`
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>Failed to Load Data</h5>
                                <p>Unable to load ward data. Please try again later.</p>
                                <button class="btn btn-primary mt-2" onclick="loadWardData()">
                                    <i class="fas fa-redo me-1"></i> Retry
                                </button>
                            </div>
                        </div>
                    `);
                    }
                });
            }

            // Function to render ward cards
            function renderWardCards(wards) {
                let container = $('#wardDataContainer');
                container.empty();

                if (!wards || wards.length === 0) {
                    container.html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-map-marked-alt"></i>
                            <h4>No Wards Found</h4>
                            <p>Get started by adding your first ward for this corporation.</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addWardModal">
                                <i class="fas fa-plus me-1"></i> Add Ward
                            </button>
                        </div>
                    </div>
                `);
                    return;
                }

                wards.forEach(ward => {
                    const droneImageUrl = ward.drone_image ?
                        `{{ asset('') }}${ward.drone_image}` :
                        '';

                    const statusClass = ward.status === 'active' ? 'status-active' : 'status-inactive';

                    // Generate roads select dropdown if roads exist
                    let roadsHtml = '';
                    if (ward.roads && ward.roads.length > 0) {
                        roadsHtml = `
                            <div class="ward-meta small mt-2">
                                <strong>
                                    <i class="fas fa-road me-1"></i>
                                    Roads (${ward.roads.length})
                                </strong>
                                <div class="mt-2">
                                    <select class="roads-select form-select-sm" data-ward-id="${ward.id}" id="roadsSelect_${ward.id}" onchange="displaySelectedRoad(this, '${ward.ward_no}')">
                                        <option value="">-- Select a road to view --</option>
                                        ${ward.roads.map(road => `<option value="${road.replace(/"/g, '&quot;')}">${road.length > 40 ? road.substring(0, 37) + '...' : road}</option>`).join('')}
                                    </select>
                                    <div class="selected-road-display" id="selectedRoadDisplay_${ward.id}">
                                        <i class="fas fa-road"></i> <span class="selected-road-text"></span>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        roadsHtml = `
                            <div class="ward-meta small mt-2 text-muted">
                                <i class="fas fa-road me-1"></i> No roads assigned
                            </div>
                        `;
                    }

                    container.append(`
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="ward-card text-center p-4">
                            <div class="mb-3">
                                ${droneImageUrl
                                    ? `<img src="${droneImageUrl}" alt="Ward ${ward.ward_no} Image" class="ward-image">`
                                    : `<div class="ward-image bg-light d-inline-flex align-items-center justify-content-center text-secondary">
                                                            <i class="fas fa-map fa-2x"></i>
                                                        </div>`
                                }
                            </div>
                            <h5 class="ward-name">Ward ${ward.ward_no}</h5>
                            <p class="ward-meta mb-1"><strong>Zone:</strong> ${ward.zone}</p>
                            <p class="ward-meta mb-2">
                                <span class="status-badge ${statusClass}">${ward.status}</span>
                            </p>

                            ${ward.extent_left || ward.extent_right || ward.extent_top || ward.extent_bottom ? `
                                                <div class="ward-meta small">
                                                    <strong>Extents:</strong><br>
                                                    L: ${ward.extent_left || 'N/A'} | R: ${ward.extent_right || 'N/A'}<br>
                                                    T: ${ward.extent_top || 'N/A'} | B: ${ward.extent_bottom || 'N/A'}
                                                </div>
                                                ` : ''}

                            ${roadsHtml}

                            <div class="ward-actions">
                                <button class="btn btn-sm btn-outline-success update-ward"
                                        data-id="${ward.id}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-ward"
                                        data-id="${ward.id}"
                                        data-name="Ward ${ward.ward_no}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                                <button class="btn btn-sm btn-outline-primary download-polygon"
                                        data-id="${ward.id}"
                                        data-name="Ward ${ward.ward_no}">
                                    <i class="fas fa-download me-1"></i> Download Polygon
                                </button>
                                <button class="btn btn-sm btn-outline-primary missing-bill"
                                        data-id="${ward.id}"
                                        data-name="Ward ${ward.ward_no}">
                                    <i class="fas fa-download me-1"></i> Download Missing Bill
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                });
            }

            window.displaySelectedRoad = function(selectElement, wardNo) {
                const selectedRoad = selectElement.value;
                const wardId = $(selectElement).data('ward-id');
                const displayDiv = $(`#selectedRoadDisplay_${wardId}`);
                if (selectedRoad) {
                    displayDiv.find('.selected-road-text').text(selectedRoad);
                    displayDiv.fadeIn();
                } else {
                    displayDiv.fadeOut();
                }
            }

            // Add ward form submission
            $('#wardForm').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.wards.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        clearValidationErrors();
                    },
                    success: function(response) {
                        if (response.success) {
                            let message = response.message;
                            if (response.polygon_processing && response.polygon_processing
                                .skipped_features > 0) {
                                message +=
                                    `. ${response.polygon_processing.skipped_features} features were skipped due to duplicate GIS IDs.`;
                            }
                            showToast('success', 'Success!', message);
                            $('#addWardModal').modal('hide');
                            $('#wardForm')[0].reset();
                            loadWardData();
                        } else {
                            showToast('error', 'Failed!', response.message ||
                                'Something went wrong.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            handleValidationErrors(xhr.responseJSON.errors);
                            showToast('error', 'Validation Error!',
                                'Please check the form for errors.');
                        } else {
                            showToast('error', 'Server Error',
                                'Something went wrong. Please try again.');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Update ward form submission
            $('#updateWardForm').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $('#updateSubmitBtn');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Updating...');

                let id = $('#update_id').val();
                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/admin/wards/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        clearValidationErrors();
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Updated!', response.message);
                            $('#updateWardModal').modal('hide');
                            loadWardData();
                        } else {
                            showToast('error', 'Failed!', response.message ||
                                'Something went wrong.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            handleValidationErrors(xhr.responseJSON.errors);
                            showToast('error', 'Validation Error!',
                                'Please check the form for errors.');
                        } else {
                            showToast('error', 'Server Error',
                                'Something went wrong. Please try again.');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Handle update button click
            $(document).on('click', '.update-ward', function(e) {
                e.stopPropagation();
                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/wards/${id}/edit`,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#updateSubmitBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            const ward = response.ward;

                            $('#update_id').val(ward.id);
                            $('#update_ward_no').val(ward.ward_no);
                            $('#update_zone').val(ward.zone);
                            $('#update_status').val(ward.status);
                            $('#update_extent_left').val(ward.extent_left);
                            $('#update_extent_right').val(ward.extent_right);
                            $('#update_extent_top').val(ward.extent_top);
                            $('#update_extent_bottom').val(ward.extent_bottom);

                            // Show current drone image if exists
                            if (ward.drone_image) {
                                $('#updateDroneImagePreview').html(`
                                <img src="{{ asset('') }}${ward.drone_image}"
                                    class="mt-2" width="100" height="100"
                                    style="border-radius:10px;object-fit:cover;border:2px solid #28a745;">
                                <p class="small text-muted mt-1">Current drone image</p>
                            `);
                            } else {
                                $('#updateDroneImagePreview').html(`
                                <p class="text-muted small mt-2">
                                    <i class="fas fa-image me-1"></i>No drone image available
                                </p>
                            `);
                            }

                            $('#updateWardModal').modal('show');
                        } else {
                            showToast('error', 'Error!', 'Failed to fetch ward details.');
                        }
                    },
                    error: function() {
                        showToast('error', 'Error!', 'Failed to fetch ward details.');
                    },
                    complete: function() {
                        $('#updateSubmitBtn').prop('disabled', false);
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-ward', function(e) {
                e.stopPropagation();
                currentDeleteId = $(this).data('id');
                const wardName = $(this).data('name');
                $('#wardNameToDelete').text(wardName);
                $('#deleteConfirmationModal').modal('show');
            });

            // Confirm deletion
            $('#confirmDeleteBtn').on('click', function() {
                if (!currentDeleteId) return;

                const deleteBtn = $(this);
                const originalText = deleteBtn.html();

                deleteBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

                $.ajax({
                    url: `/admin/wards/${currentDeleteId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Deleted!', response.message);
                            $('#deleteConfirmationModal').modal('hide');
                            loadWardData();
                        } else {
                            showToast('error', 'Error!', response.message ||
                                'Failed to delete ward.');
                        }
                    },
                    error: function(xhr) {
                        showToast('error', 'Error!', xhr.responseJSON?.message ||
                            'Failed to delete ward.');
                    },
                    complete: function() {
                        deleteBtn.prop('disabled', false).html(originalText);
                        currentDeleteId = null;
                    }
                });
            });

            // Handle download polygon button click
            $(document).on('click', '.download-polygon', function(e) {
                e.stopPropagation();
                const wardId = $(this).data('id');
                const wardName = $(this).data('name');

                // Show loading toast
                showToast('info', 'Processing', `Preparing download for ${wardName}...`);

                // Trigger download
                window.location.href = `/admin/wards/${wardId}/download-polygon`;
            });
            $(document).on('click', '.missing-bill', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const wardId = $(this).data('id');
                const wardName = $(this).data('name');
                const roadname = $(`#roadsSelect_${wardId}`).val();

                if (!roadname) {
                    showToast('warning', 'No Road Selected',
                        'Please select a road to download missing bill.');
                    return;
                }

                showToast('info', 'Processing', `Preparing missing bill download for ${roadname}...`);

                $.ajax({
                    url: `/admin/wards/${wardId}/download-missing-bill?roadname=${encodeURIComponent(roadname)}`,
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        roadname: roadname
                    },
                    xhrFields: {
                        responseType: 'blob' // Important for file download
                    },
                    success: function(response, status, xhr) {
                        // Get filename from Content-Disposition header
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = 'missing_bill_data.csv';
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            const matches = filenameRegex.exec(disposition);
                            if (matches != null && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }

                        // Create blob link to download
                        const url = window.URL.createObjectURL(new Blob([response]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);

                        showToast('success', 'Success!',
                            'Missing bill data downloaded successfully.');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to download missing bill.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {}
                        showToast('error', 'Error!', errorMessage);
                    }
                });
            });
            // Utility functions
            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').html('');
            }

            function handleValidationErrors(errors) {
                clearValidationErrors();
                for (let key in errors) {
                    let input = $(`[name="${key}"]`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').html(errors[key][0]);
                }
            }

            function showToast(type, title, message) {
                const toast = $('#liveToast');
                toast.removeClass('success error warning info');
                toast.addClass(type);

                toast.find('.toast-title').text(title);
                toast.find('.toast-message').text(message);

                // Change icon color based on type
                let iconColor = '';
                if (type === 'success') iconColor = '#198754';
                if (type === 'error') iconColor = '#dc3545';
                if (type === 'info') iconColor = '#0dcaf0';

                toast.find('.toast-icon').css('color', iconColor);

                const toastInstance = new bootstrap.Toast(toast[0], {
                    autohide: true,
                    delay: 3000
                });
                toastInstance.show();
            }

            // Reset forms when modals are closed
            $('.modal').on('hidden.bs.modal', function() {
                clearValidationErrors();
                $('.modal form').trigger('reset');
                $('#updateDroneImagePreview').html('');
            });

            // Initialize page
            loadWardData();
        });
    </script>
@endsection
