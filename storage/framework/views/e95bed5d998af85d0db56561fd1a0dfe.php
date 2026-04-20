
<?php $__env->startSection('title', 'Corporation Data'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary"><i class="fas fa-city me-2"></i>Corporation Data</h1>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCorporationModal">
            <i class="fas fa-plus me-1"></i> Add Corporation
        </button>
    </div>

    <!-- Corporation Cards Container -->
    <div class="row g-4" id="corporationDataContainer">
        <div class="col-12 text-center text-muted py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading corporation data...</p>
        </div>
    </div>
</div>

<!-- Add Corporation Modal -->
<div class="modal fade" id="addCorporationModal" tabindex="-1" aria-labelledby="addCorporationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCorporationModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add Corporation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="corporationForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Corporation Name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation Code</label>
                            <input type="text" class="form-control" name="code" placeholder="Enter Corporation Code" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">District</label>
                            <input type="text" class="form-control" name="district" placeholder="Enter District Name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">State</label>
                            <input type="text" class="form-control" name="state" placeholder="Enter State Name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 2MB</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Boundary (GeoJSON File)</label>
                            <input type="file" class="form-control" name="boundary" accept=".geojson,application/json">
                            <div class="form-text">Upload GeoJSON file for boundary data</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- MIS -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">MIS</label>
                            <input type="file" class="form-control" name="mis" >                           
                            <div class="form-text">Upload MIS Excel file</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Watertax -->
                         <div class="col-md-6">
                            <label class="form-label fw-bold">Watertax</label>
                            <input type="file" class="form-control" name="watertax" >                           
                            <div class="form-text">Upload Watertax Excel file</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- UGD - FIXED NAME -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">UGD</label>
                            <input type="file" class="form-control" name="ugd" >                           
                            <div class="form-text">Upload UGD Excel file</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer mt-4 px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Save Corporation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Corporation Modal -->
<div class="modal fade" id="updateCorporationModal" tabindex="-1" aria-labelledby="updateCorporationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="updateCorporationModalLabel">
                    <i class="fas fa-edit me-2"></i>Update Corporation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateCorporationForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="id" id="update_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation Name</label>
                            <input type="text" class="form-control" name="name" id="update_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation Code</label>
                            <input type="text" class="form-control" name="code" id="update_code" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">District</label>
                            <input type="text" class="form-control" name="district" id="update_district" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">State</label>
                            <input type="text" class="form-control" name="state" id="update_state" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <div id="updateLogoPreview" class="mt-2"></div>
                            <div class="form-text">Leave empty to keep current logo</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Boundary (GeoJSON File)</label>
                            <input type="file" class="form-control" name="boundary" accept=".geojson,application/json">
                            <div class="form-text">Leave empty to keep current boundary</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- MIS -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">MIS</label>
                            <input type="file" class="form-control" name="mis" >                           
                            <div class="form-text">Leave empty to keep current MIS</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Watertax -->
                         <div class="col-md-6">
                            <label class="form-label fw-bold">Watertax</label>
                            <input type="file" class="form-control" name="watertax" >                           
                            <div class="form-text">Leave empty to keep current Watertax</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- UGD - FIXED NAME -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">UGD</label>
                            <input type="file" class="form-control" name="ugd" >                           
                            <div class="form-text">Leave empty to keep current UGD</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer mt-4 px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="updateSubmitBtn">
                            <i class="fas fa-save me-1"></i> Update Corporation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="corporationNameToDelete"></strong>?</p>
                <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Delete Corporation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .corp-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease-in-out;
        overflow: hidden;
        height: 100%;
    }

    .corp-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        border-color: #007bff;
    }

    .corp-logo {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #007bff;
        background: #fff;
    }

    .corp-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .corp-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .corp-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
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

    /* Toast Styles */
    .toast.success .toast-icon {
        color: #198754;
    }

    .toast.error .toast-icon {
        color: #dc3545;
    }

    .toast.warning .toast-icon {
        color: #ffc107;
    }

    .toast.info .toast-icon {
        color: #0dcaf0;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
        let currentDeleteId = null;

        // Load corporation data on page load
        loadCorporationData();

        // Function to load corporation data
        function loadCorporationData() {
            $.ajax({
                url: '<?php echo e(route("admin.corporation-list")); ?>',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#corporationDataContainer').html(`
                        <div class="col-12 text-center text-muted py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading corporation data...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    renderCorporationCards(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading corporation data:', error);
                    $('#corporationDataContainer').html(`
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>Failed to Load Data</h5>
                                <p>Unable to load corporation data. Please try again later.</p>
                                <button class="btn btn-primary mt-2" onclick="loadCorporationData()">
                                    <i class="fas fa-redo me-1"></i> Retry
                                </button>
                            </div>
                        </div>
                    `);
                }
            });
        }

        // Function to render corporation cards
        function renderCorporationCards(corporations) {
            let container = $('#corporationDataContainer');
            container.empty();

            if (!corporations || corporations.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-city"></i>
                            <h4>No Corporations Found</h4>
                            <p>Get started by adding your first corporation.</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCorporationModal">
                                <i class="fas fa-plus me-1"></i> Add Corporation
                            </button>
                        </div>
                    </div>
                `);
                return;
            }

            corporations.forEach(corp => {
                const logoUrl = corp.logo ? `<?php echo e(Storage::url('')); ?>${corp.logo}` : '';
                container.append(`
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="corp-card text-center p-4">
                            <div class="mb-3">
                                ${logoUrl 
                                    ? `<img src="${logoUrl}" alt="${corp.name} Logo" class="corp-logo">`
                                    : `<div class="corp-logo bg-light d-inline-flex align-items-center justify-content-center text-secondary">
                                        <i class="fas fa-building fa-2x"></i>
                                    </div>`
                                }
                            </div>
                            <h5 class="corp-name">${corp.name}</h5>
                            <p class="corp-meta mb-1"><strong>Code:</strong> ${corp.code}</p>
                            <p class="corp-meta mb-1"><strong>District:</strong> ${corp.district}</p>
                            <p class="corp-meta mb-3"><strong>State:</strong> ${corp.state}</p>
                            
                            <div class="corp-actions">
                                <button class="btn btn-sm btn-outline-success update-corporation" 
                                        data-id="${corp.id}" 
                                        data-name="${corp.name}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-corporation" 
                                        data-id="${corp.id}" 
                                        data-name="${corp.name}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                                
                                <a class="btn btn-sm btn-outline-primary "
                                    href="<?php echo e(url('admin/ward')); ?>/${corp.id}"
                                    data-id="${corp.id}"
                                    data-name="${corp.name}">
                                    <i class="fas fa-database me-1"></i> Wards
                                </a>
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        // Toast notification function
        function showToast(type, title, message) {
            const toast = $('#liveToast');
            const toastIcon = toast.find('.toast-icon');
            const toastTitle = toast.find('.toast-title');
            const toastMessage = toast.find('.toast-message');
            
            // Remove all existing classes and add the appropriate ones
            toast.removeClass('success error warning info').addClass(type);
            
            // Set icon color based on type
            const iconColors = {
                'success': '#198754',
                'error': '#dc3545', 
                'warning': '#ffc107',
                'info': '#0dcaf0'
            };
            
            toastIcon.css('color', iconColors[type] || '#6c757d');
            toastTitle.text(title);
            toastMessage.text(message);
            
            // Show the toast
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
        }

        // Add corporation form submission
        $('#corporationForm').on('submit', function(e) {
            e.preventDefault();
            const submitBtn = $('#submitBtn');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            let formData = new FormData(this);

            $.ajax({
                url: '<?php echo e(route("admin.corporation-store")); ?>',
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
                        showToast('success', 'Success!', response.message);
                        $('#addCorporationModal').modal('hide');
                        $('#corporationForm')[0].reset();
                        loadCorporationData();
                    } else {
                        showToast('error', 'Failed!', response.message || 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        handleValidationErrors(xhr.responseJSON.errors);
                        showToast('error', 'Validation Error!', 'Please check the form for errors.');
                    } else {
                        showToast('error', 'Server Error', 'Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Update corporation form submission
        $('#updateCorporationForm').on('submit', function(e) {
            e.preventDefault();
            const submitBtn = $('#updateSubmitBtn');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...');

            let id = $('#update_id').val();
            let formData = new FormData(this);

            // Add _method for PUT request
            formData.append('_method', 'PUT');

            $.ajax({
                url: `/admin/corporation/${id}`,
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
                        $('#updateCorporationModal').modal('hide');
                        loadCorporationData();
                    } else {
                        showToast('error', 'Failed!', response.message || 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        handleValidationErrors(xhr.responseJSON.errors);
                        showToast('error', 'Validation Error!', 'Please check the form for errors.');
                    } else {
                        showToast('error', 'Server Error', 'Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Handle update button click
        $(document).on('click', '.update-corporation', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');

            $.ajax({
                url: `/admin/corporation/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#updateSubmitBtn').prop('disabled', true);
                },
                success: function(response) {
                    $('#update_id').val(response.id);
                    $('#update_name').val(response.name);
                    $('#update_code').val(response.code);
                    $('#update_district').val(response.district);
                    $('#update_state').val(response.state);

                    // Show current logo if exists
                    if (response.logo) {
                        $('#updateLogoPreview').html(`
                            <img src="<?php echo e(Storage::url('')); ?>${response.logo}" class="mt-2" width="100" height="100" 
                                 style="border-radius:10px;object-fit:cover;border:2px solid #28a745;">
                            <p class="small text-muted mt-1">Current logo</p>
                        `);
                    } else {
                        $('#updateLogoPreview').html(`
                            <p class="text-muted small mt-2">
                                <i class="fas fa-image me-1"></i>No logo available
                            </p>
                        `);
                    }

                    $('#updateCorporationModal').modal('show');
                },
                error: function() {
                    showToast('error', 'Error!', 'Failed to fetch corporation details.');
                },
                complete: function() {
                    $('#updateSubmitBtn').prop('disabled', false);
                }
            });
        });

        // Handle delete button click
        $(document).on('click', '.delete-corporation', function() {
            currentDeleteId = $(this).data('id');
            const corporationName = $(this).data('name');

            $('#corporationNameToDelete').text(corporationName);
            $('#deleteConfirmationModal').modal('show');
        });

        // Confirm deletion
        $('#confirmDeleteBtn').on('click', function() {
            if (!currentDeleteId) return;

            const deleteBtn = $(this);
            const originalText = deleteBtn.html();

            deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

            $.ajax({
                url: `/admin/corporation/${currentDeleteId}`,
                type: 'DELETE',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Deleted!', response.message);
                        $('#deleteConfirmationModal').modal('hide');
                        loadCorporationData();
                    } else {
                        showToast('error', 'Error!', response.message || 'Failed to delete corporation.');
                    }
                },
                error: function(xhr) {
                    showToast('error', 'Error!', xhr.responseJSON?.message || 'Failed to delete corporation.');
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

        // Reset forms when modals are closed
        $('.modal').on('hidden.bs.modal', function() {
            clearValidationErrors();
            $('.modal form').trigger('reset');
            $('#updateLogoPreview').html('');
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\testinggayu\resources\views/admin/corporationdata.blade.php ENDPATH**/ ?>