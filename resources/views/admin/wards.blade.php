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
                            <!-- Corporation ID (Display only) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Corporation</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $corporation->name }} ({{ $corporation->code }})" readonly>
                                <small class="form-text text-muted">Corporation ID: {{ $corporationId }}</small>
                            </div>

                            <!-- Ward No -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Ward No</label>
                                <input type="text" class="form-control" name="ward_no" id="ward_no"
                                    placeholder="Enter Ward Number" required>
                                <div class="invalid-feedback">Please enter ward number.</div>
                            </div>

                            <!-- Zone Dropdown -->
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

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>

                            <!-- Extent Fields -->
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

                            <!-- File Uploads -->
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-file-upload me-2"></i>File Uploads
                                </h6>
                            </div>

                            <!-- Drone Image -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Drone Image</label>
                                <input type="file" class="form-control" name="drone_image" accept="image/*">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formats: JPG, PNG, GIF. Max: 2MB
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Boundary File -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Boundary File</label>
                                <input type="file" class="form-control" name="boundary"
                                    accept=".geojson,application/json">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    GeoJSON format. Max: 5MB
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Polygon File -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Polygon File</label>
                                <input type="file" class="form-control" name="polygon"
                                    accept=".geojson,application/json">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    GeoJSON format. Max: 5MB
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Line File</label>
                                <input type="file" class="form-control" name="line"
                                    accept=".geojson,application/json">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    GeoJSON format. Max: 5MB
                                </div>
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
                            <!-- Corporation ID (Display only) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Corporation</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $corporation->name }} ({{ $corporation->code }})" readonly>
                            </div>

                            <!-- Ward No -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Ward No</label>
                                <input type="text" class="form-control" name="ward_no" id="update_ward_no" required>
                                <div class="invalid-feedback">Please enter ward number.</div>
                            </div>

                            <!-- Zone Dropdown -->
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

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">Status</label>
                                <select class="form-select" name="status" id="update_status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>

                            <!-- Extent Fields -->
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

                            <!-- File Uploads -->
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-file-upload me-2"></i>File Uploads
                                </h6>
                            </div>

                            <!-- Drone Image -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Drone Image</label>
                                <input type="file" class="form-control" name="drone_image" accept="image/*">
                                <div id="updateDroneImagePreview" class="mt-2"></div>
                                <div class="form-text">Leave empty to keep current image</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Boundary File -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Boundary File</label>
                                <input type="file" class="form-control" name="boundary"
                                    accept=".geojson,application/json">
                                <div class="form-text">Leave empty to keep current boundary</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Polygon File -->
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

    <!-- Roads Detail Modal -->
    <div class="modal fade" id="roadsModal" tabindex="-1" aria-labelledby="roadsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="roadsModalLabel">
                        <i class="fas fa-road me-2"></i>Ward Roads
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="roadsListContainer">
                        <!-- Roads will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            cursor: pointer;
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
            cursor: default;
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

        /* Roads styling */
        .roads-list {
            max-height: 60px;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: center;
            margin-top: 5px;
        }

        .road-tag {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 12px;
            white-space: nowrap;
        }

        .road-tag.more-tag {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .road-tag.more-tag:hover {
            background-color: #0056b3;
        }

        .roads-list::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .roads-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .roads-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .roads-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .road-item {
            padding: 8px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }

        .road-item:last-child {
            border-bottom: none;
        }

        .road-item i {
            color: #007bff;
            margin-right: 8px;
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

                    // Generate roads HTML if roads exist
                    let roadsHtml = '';
                    if (ward.roads && ward.roads.length > 0) {
                        const roadList = ward.roads.slice(0, 3); // Show first 3 roads
                        const remainingCount = ward.roads.length - 3;

                        roadsHtml = `
                            <div class="ward-meta small mt-2">
                                <strong><i class="fas fa-road me-1"></i> Roads (${ward.roads.length}):</strong>
                                <div class="roads-list">
                                    ${roadList.map(road => `<span class="road-tag" title="${road}">${road.length > 15 ? road.substring(0, 12) + '...' : road}</span>`).join('')}
                                    ${remainingCount > 0 ? `<span class="road-tag more-tag" data-ward-id="${ward.id}" data-ward-name="Ward ${ward.ward_no}" data-roads='${JSON.stringify(ward.roads)}'>+${remainingCount} more</span>` : ''}
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
                        <div class="ward-card text-center p-4" data-ward-id="${ward.id}" data-ward-name="Ward ${ward.ward_no}" data-roads='${JSON.stringify(ward.roads || [])}'>
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
                            </div>
                        </div>
                    </div>
                `);
                });
            }

            // Show roads modal when clicking on "more" tag or card
            $(document).on('click', '.more-tag', function(e) {
                e.stopPropagation();
                const wardName = $(this).data('ward-name');
                const roads = $(this).data('roads');
                showRoadsModal(wardName, roads);
            });

            // Optional: Show roads modal when clicking on the ward card (excluding buttons)
            $(document).on('click', '.ward-card', function(e) {
                // Don't trigger if clicking on buttons or action buttons area
                if ($(e.target).closest('.ward-actions').length || $(e.target).closest('.more-tag').length) {
                    return;
                }

                const wardName = $(this).data('ward-name');
                const roads = $(this).data('roads');

                if (roads && roads.length > 0) {
                    showRoadsModal(wardName, roads);
                }
            });

            function showRoadsModal(wardName, roads) {
                if (!roads || roads.length === 0) return;

                $('#roadsModalLabel').text(`${wardName} - Roads (${roads.length})`);
                let roadsHtml = '<div class="list-group list-group-flush">';
                roads.forEach(road => {
                    roadsHtml += `
                        <div class="list-group-item road-item">
                            <i class="fas fa-road"></i>
                            <strong>${road}</strong>
                        </div>
                    `;
                });
                roadsHtml += '</div>';
                $('#roadsListContainer').html(roadsHtml);
                $('#roadsModal').modal('show');
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
                toast.find('.toast-icon').removeClass().addClass(`fas fa-circle me-2 toast-icon`);

                const toastInstance = new bootstrap.Toast(toast[0]);
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
