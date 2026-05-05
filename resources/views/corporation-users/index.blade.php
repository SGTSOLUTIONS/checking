@extends('layouts.admin-layout')
@section('title', 'Corporation Users Management')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary">
            <i class="fas fa-users me-2"></i>Corporation Users
        </h1>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-1"></i> Add User
        </button>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Corporation</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading users...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add Corporation User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Full Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter full name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" placeholder="Enter phone number">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Administrator</option>
                                <option value="team_leader">Team Leader</option>
                                <option value="surveyor">Surveyor</option>
                                <option value="dc">District Commissioner</option>
                                <option value="commissioner">Commissioner</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation</label>
                            <select class="form-select" name="corporation_id" required>
                                <option value="">Select Corporation</option>
                                @foreach($corporations as $corporation)
                                    <option value="{{ $corporation->id }}">{{ $corporation->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" class="form-control" name="city" placeholder="Enter city">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Password</label>
                            <input type="password" class="form-control" name="password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <input type="file" class="form-control" name="profile" accept="image/*">
                            <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 2MB</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer mt-4 px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update User Modal -->
<div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="updateUserModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Update Corporation User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateUserForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="update_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Full Name</label>
                            <input type="text" class="form-control" name="name" id="update_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Email Address</label>
                            <input type="email" class="form-control" name="email" id="update_email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" id="update_phone">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Role</label>
                            <select class="form-select" name="role" id="update_role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Administrator</option>
                                <option value="team_leader">Team Leader</option>
                                <option value="surveyor">Surveyor</option>
                                <option value="dc">District Commissioner</option>
                                <option value="commissioner">Commissioner</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation</label>
                            <select class="form-select" name="corporation_id" id="update_corporation_id" required>
                                <option value="">Select Corporation</option>
                                @foreach($corporations as $corporation)
                                    <option value="{{ $corporation->id }}">{{ $corporation->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" class="form-control" name="city" id="update_city">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender</label>
                            <select class="form-select" name="gender" id="update_gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" id="update_date_of_birth">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Status</label>
                            <select class="form-select" name="status" id="update_status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" class="form-control" name="password">
                            <div class="form-text">Leave empty to keep current password</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <input type="file" class="form-control" name="profile" accept="image/*">
                            <div id="updateProfilePreview" class="mt-2"></div>
                            <div class="form-text">Leave empty to keep current profile picture</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer mt-4 px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="updateSubmitBtn">
                            <i class="fas fa-save me-1"></i> Update User
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
                <p>Are you sure you want to delete <strong id="userNameToDelete"></strong>?</p>
                <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewUserModalLabel">
                    <i class="fas fa-eye me-2"></i>User Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewUserContent">
                <!-- User details will be loaded here -->
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
    .required::after {
        content: " *";
        color: #dc3545;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #e9ecef;
    }

    .user-avatar-placeholder {
        width: 40px;
        height: 40px;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e9ecef;
    }

    .action-buttons .btn {
        margin: 0 2px;
        padding: 4px 8px;
    }

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }

    .toast-container {
        z-index: 9999;
    }

    .modal-lg {
        max-width: 800px;
    }

    /* Loading States */
    .btn-loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Profile Preview */
    #updateProfilePreview img {
        max-width: 100px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let currentDeleteId = null;

    // Load users on page load
    loadUsers();

    function loadUsers() {
        $.ajax({
            url: '{{ route("admin.corporation-user-list") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderUsersTable(response.data);
                } else {
                    showError('Failed to load users');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                $('#usersTableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Failed to load users. Please try again.</p>
                            <button class="btn btn-sm btn-primary" onclick="location.reload()">
                                <i class="fas fa-redo me-1"></i> Retry
                            </button>
                        </td>
                    </tr>
                `);
                showToast('error', 'Error', 'Failed to load users. Please refresh the page.');
            }
        });
    }

    function renderUsersTable(users) {
        let tbody = $('#usersTableBody');
        tbody.empty();

        if (!users || users.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                        <h5>No Users Found</h5>
                        <p>Click the "Add User" button to create your first corporation user.</p>
                    </td>
                </tr>
            `);
            return;
        }

        users.forEach(user => {
            // Profile image HTML with full URL
            let profileHtml;
            if (user.profile) {
                const profileUrl = '{{ url("/") }}/' + user.profile;
                profileHtml = `<img src="${profileUrl}" alt="${escapeHtml(user.name)}" class="user-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"><div class="user-avatar-placeholder" style="display:none;"><i class="fas fa-user text-secondary"></i></div>`;
            } else {
                profileHtml = `<div class="user-avatar-placeholder"><i class="fas fa-user text-secondary"></i></div>`;
            }

            // Status badge
            const statusBadge = user.status === 'active'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            // Role badge with colors
            let roleBadge = '';
            switch(user.role) {
                case 'admin':
                    roleBadge = '<span class="badge bg-primary">Admin</span>';
                    break;
                case 'team_leader':
                    roleBadge = '<span class="badge bg-warning text-dark">Team Leader</span>';
                    break;
                case 'surveyor':
                    roleBadge = '<span class="badge bg-info">Surveyor</span>';
                    break;
                case 'dc':
                    roleBadge = '<span class="badge bg-secondary">District Commissioner</span>';
                    break;
                case 'commissioner':
                    roleBadge = '<span class="badge bg-dark">Commissioner</span>';
                    break;
                default:
                    roleBadge = '<span class="badge bg-secondary">' + user.role + '</span>';
            }

            tbody.append(`
                <tr>
                    <td>${user.id}</td>
                    <td>${profileHtml}</td>
                    <td><strong>${escapeHtml(user.name)}</strong></td>
                    <td>${escapeHtml(user.email)}</td>
                    <td>${user.phone || '-'}</td>
                    <td>${user.corporation ? escapeHtml(user.corporation.name) : '-'}</td>
                    <td>${roleBadge}</td>
                    <td>${statusBadge}</td>
                    <td class="action-buttons">
                        <button class="btn btn-sm btn-outline-info view-user" data-id="${user.id}" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success edit-user" data-id="${user.id}" title="Edit User">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-user"
                                data-id="${user.id}"
                                data-name="${escapeHtml(user.name)}"
                                title="Delete User">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function showToast(type, title, message) {
        const toast = $('#liveToast');
        toast.removeClass('success error warning info').addClass(type);

        const iconColors = {
            'success': '#198754',
            'error': '#dc3545',
            'warning': '#ffc107',
            'info': '#0dcaf0'
        };

        toast.find('.toast-icon').css('color', iconColors[type] || '#6c757d');
        toast.find('.toast-title').text(title);
        toast.find('.toast-message').text(message);

        const bsToast = new bootstrap.Toast(toast[0], {
            autohide: true,
            delay: 5000
        });
        bsToast.show();
    }

    function showSuccess(message) {
        showToast('success', 'Success!', message);
    }

    function showError(message) {
        showToast('error', 'Error!', message);
    }

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

    // Add User Form Submit
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        let formData = new FormData(this);

        $.ajax({
            url: '{{ route("admin.corporation-user-store") }}',
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
                    showSuccess(response.message);
                    $('#addUserModal').modal('hide');
                    $('#userForm')[0].reset();
                    loadUsers();
                } else {
                    showError(response.message || 'Something went wrong.');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    handleValidationErrors(xhr.responseJSON.errors);
                    showError('Please check the form for errors.');
                } else {
                    showError('Something went wrong. Please try again.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Edit User - Load data
    $(document).on('click', '.edit-user', function() {
        let id = $(this).data('id');

        // Reset form and clear validation
        clearValidationErrors();
        $('#updateProfilePreview').html('');

        $.ajax({
            url: `/admin/corporation-user/${id}/edit`,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#updateSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Loading...');
            },
            success: function(response) {
                $('#update_id').val(response.id);
                $('#update_name').val(response.name);
                $('#update_email').val(response.email);
                $('#update_phone').val(response.phone);
                $('#update_role').val(response.role);
                $('#update_corporation_id').val(response.corporation_id);
                $('#update_city').val(response.city);
                $('#update_gender').val(response.gender);
                $('#update_date_of_birth').val(response.date_of_birth);
                $('#update_status').val(response.status);

                // Show current profile picture
                if (response.profile) {
                    const profileUrl = '{{ url("/") }}/' + response.profile;
                    $('#updateProfilePreview').html(`
                        <div class="border rounded p-2 d-inline-block">
                            <img src="${profileUrl}" width="80" height="80"
                                 style="border-radius:10px;object-fit:cover;border:2px solid #28a745;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <p class="small text-muted mt-1 mb-0">Current profile picture</p>
                        </div>
                    `);
                } else {
                    $('#updateProfilePreview').html(`
                        <p class="text-muted small mt-2">
                            <i class="fas fa-image me-1"></i>No profile picture available
                        </p>
                    `);
                }

                $('#updateUserModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                showError('Failed to fetch user details.');
            },
            complete: function() {
                $('#updateSubmitBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update User');
            }
        });
    });

    // Update User Form Submit
    $('#updateUserForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#updateSubmitBtn');
        const originalText = submitBtn.html();
        let id = $('#update_id').val();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...');

        let formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: `/admin/corporation-user/${id}`,
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
                    showSuccess(response.message);
                    $('#updateUserModal').modal('hide');
                    loadUsers();
                } else {
                    showError(response.message || 'Something went wrong.');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    handleValidationErrors(xhr.responseJSON.errors);
                    showError('Please check the form for errors.');
                } else {
                    showError('Something went wrong. Please try again.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // View User Details
    $(document).on('click', '.view-user', function() {
        let id = $(this).data('id');

        $('#viewUserContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading user details...</p>
            </div>
        `);

        $.ajax({
            url: `/admin/corporation-user/${id}/edit`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let profileHtml;
                if (response.profile) {
                    const profileUrl = '{{ url("/") }}/' + response.profile;
                    profileHtml = `<img src="${profileUrl}" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #0dcaf0;">`;
                } else {
                    profileHtml = `<div class="mb-3"><i class="fas fa-user-circle fa-5x text-secondary"></i></div>`;
                }

                const statusBadge = response.status === 'active'
                    ? '<span class="badge bg-success fs-6">Active</span>'
                    : '<span class="badge bg-danger fs-6">Inactive</span>';

                let roleLabel = '';
                switch(response.role) {
                    case 'admin': roleLabel = 'Administrator'; break;
                    case 'team_leader': roleLabel = 'Team Leader'; break;
                    case 'surveyor': roleLabel = 'Surveyor'; break;
                    case 'dc': roleLabel = 'District Commissioner'; break;
                    case 'commissioner': roleLabel = 'Commissioner'; break;
                    default: roleLabel = response.role;
                }

                let genderLabel = '';
                switch(response.gender) {
                    case 'male': genderLabel = 'Male'; break;
                    case 'female': genderLabel = 'Female'; break;
                    case 'other': genderLabel = 'Other'; break;
                    default: genderLabel = '-';
                }

                $('#viewUserContent').html(`
                    <div class="text-center">
                        ${profileHtml}
                        <h4 class="mt-2">${escapeHtml(response.name)}</h4>
                        ${statusBadge}
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong><br>
                                <span class="text-muted">${escapeHtml(response.email)}</span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-phone me-2 text-primary"></i>Phone:</strong><br>
                                <span class="text-muted">${response.phone || '-'}</span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-building me-2 text-primary"></i>Corporation:</strong><br>
                                <span class="text-muted">${response.corporation ? escapeHtml(response.corporation.name) : '-'}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong><i class="fas fa-user-tag me-2 text-primary"></i>Role:</strong><br>
                                <span class="text-muted">${roleLabel}</span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-city me-2 text-primary"></i>City:</strong><br>
                                <span class="text-muted">${response.city || '-'}</span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-venus-mars me-2 text-primary"></i>Gender:</strong><br>
                                <span class="text-muted">${genderLabel}</span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-birthday-cake me-2 text-primary"></i>Date of Birth:</strong><br>
                                <span class="text-muted">${response.date_of_birth || '-'}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Joined: ${new Date(response.created_at).toLocaleDateString()} |
                                Last Updated: ${new Date(response.updated_at).toLocaleDateString()}
                            </small>
                        </div>
                    </div>
                `);
                $('#viewUserModal').modal('show');
            },
            error: function() {
                showError('Failed to fetch user details.');
                $('#viewUserModal').modal('hide');
            }
        });
    });

    // Delete User
    $(document).on('click', '.delete-user', function() {
        currentDeleteId = $(this).data('id');
        const userName = $(this).data('name');
        $('#userNameToDelete').text(userName);
        $('#deleteConfirmationModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        if (!currentDeleteId) return;

        const deleteBtn = $(this);
        const originalText = deleteBtn.html();

        deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

        $.ajax({
            url: `/admin/corporation-user/${currentDeleteId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    $('#deleteConfirmationModal').modal('hide');
                    loadUsers();
                } else {
                    showError(response.message || 'Failed to delete user.');
                }
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || 'Failed to delete user.');
            },
            complete: function() {
                deleteBtn.prop('disabled', false).html(originalText);
                currentDeleteId = null;
            }
        });
    });

    // Reset forms when modals close
    $('.modal').on('hidden.bs.modal', function() {
        clearValidationErrors();
        $(this).find('form').trigger('reset');
        $('#updateProfilePreview').html('');
    });

    // Password confirmation validation
    $('input[name="password"], input[name="password_confirmation"]').on('keyup', function() {
        const password = $('input[name="password"]').val();
        const confirm = $('input[name="password_confirmation"]').val();
        if (password !== confirm) {
            $('input[name="password_confirmation"]').addClass('is-invalid');
            $('input[name="password_confirmation"]').siblings('.invalid-feedback').html('Passwords do not match.');
        } else {
            $('input[name="password_confirmation"]').removeClass('is-invalid');
            $('input[name="password_confirmation"]').siblings('.invalid-feedback').html('');
        }
    });
});
</script>
@endsection
