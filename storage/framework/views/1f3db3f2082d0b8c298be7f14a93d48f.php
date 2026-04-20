<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-primary"><i class="fas fa-users me-2"></i>User Management</h1>
            <p class="text-muted mb-0">Manage system users and their permissions</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-1"></i> Add User
            </button>
        </div>
    </div>

    <!-- Users Cards Container -->
    <div class="row g-4" id="userDataContainer">
        <div class="col-12 text-center text-muted py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading user data...</p>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user me-2"></i>Basic Information</h6>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Full Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter full name" required>
                            <div class="invalid-feedback">Please enter user's name.</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                            <div class="invalid-feedback">Please enter a password (min 8 characters).</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" required>
                            <div class="invalid-feedback">Please confirm the password.</div>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Role</label>
                            <select class="form-select" name="role" id="role" required>
                                <option value="">Select Role</option>
                                <?php $__currentLoopData = \App\Enums\RoleEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->value); ?>"><?php echo e(ucfirst($role->value)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Status</label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="">Select Status</option>
                                <?php $__currentLoopData = \App\Enums\ActiveStatusEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->value); ?>"><?php echo e(ucfirst($status->value)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">Please select status.</div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Additional Information</h6>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone number">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" class="form-control" name="city" id="city" placeholder="Enter city">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender</label>
                            <select class="form-select" name="gender" id="gender">
                                <option value="">Select Gender</option>
                                <?php $__currentLoopData = \App\Enums\GenderEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gender->value); ?>"><?php echo e(ucfirst($gender->value)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" id="date_of_birth">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Profile Image -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-camera me-2"></i>Profile Image</h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <input type="file" class="form-control" name="profile" accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formats: JPG, PNG, GIF. Max: 2MB
                            </div>
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
                    <i class="fas fa-edit me-2"></i>Update User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateUserForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="id" id="update_id">

                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user me-2"></i>Basic Information</h6>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Full Name</label>
                            <input type="text" class="form-control" name="name" id="update_name" required>
                            <div class="invalid-feedback">Please enter user's name.</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Email Address</label>
                            <input type="email" class="form-control" name="email" id="update_email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" name="password" id="update_password" placeholder="Leave empty to keep current">
                            <div class="form-text">Leave empty to keep current password</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="update_password_confirmation" placeholder="Confirm new password">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Role</label>
                            <select class="form-select" name="role" id="update_role" required>
                                <option value="">Select Role</option>
                                <?php $__currentLoopData = \App\Enums\RoleEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->value); ?>"><?php echo e(ucfirst($role->value)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Status</label>
                            <select class="form-select" name="status" id="update_status" required>
                                <option value="">Select Status</option>
                                <?php $__currentLoopData = \App\Enums\ActiveStatusEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->value); ?>"><?php echo e(ucfirst($status->value)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">Please select status.</div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Additional Information</h6>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="update_phone">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" class="form-control" name="city" id="update_city">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender</label>
                            <select class="form-select" name="gender" id="update_gender">
                                <option value="">Select Gender</option>
                                <?php $__currentLoopData = \App\Enums\GenderEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gender->value); ?>"><?php echo e(ucfirst($gender->value)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" id="update_date_of_birth">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Profile Image -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-camera me-2"></i>Profile Image</h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <input type="file" class="form-control" name="profile" accept="image/*">
                            <div id="updateProfilePreview" class="mt-2"></div>
                            <div class="form-text">Leave empty to keep current image</div>
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
                <p>Are you sure you want to delete user <strong id="userNameToDelete"></strong>?</p>
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

<!-- Assign Team Modal -->
<div class="modal fade" id="assignTeamModal" tabindex="-1" aria-labelledby="assignTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="assignTeamModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Assign Team Leader to Ward
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignTeamForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="team_leader_id" id="assign_team_leader_id">
                    <input type="hidden" name="leader_name" id="assign_leader_name">

                    <div class="row g-3">
                        <!-- User Information -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Assigning team leader: <strong id="assignUserNameText"></strong>
                            </div>
                        </div>

                        <!-- Corporation Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Corporation</label>
                            <select class="form-select" name="corporation_id" id="corporation_id" required>
                                <option value="">Select Corporation</option>
                                <!-- Corporations will be loaded via AJAX -->
                            </select>
                            <div class="invalid-feedback">Please select a corporation.</div>
                        </div>

                        <!-- Ward Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Ward</label>
                            <select class="form-select" name="ward_id" id="ward_id" required disabled>
                                <option value="">Select Ward</option>
                                <!-- Wards will be loaded based on corporation selection -->
                            </select>
                            <div class="invalid-feedback">Please select a ward.</div>
                        </div>

                        <!-- Team Information -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-users me-2"></i>Team Information</h6>
                        </div>

                        <!-- Team Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Team Name</label>
                            <input type="text" class="form-control" name="name" id="team_name"
                                placeholder="Enter team name (e.g., Team A, Survey Team 1)" required>
                            <div class="invalid-feedback">Please enter a team name.</div>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="contact_number"
                                placeholder="Enter contact number" required>
                            <div class="invalid-feedback">Please enter a contact number.</div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Team Status</label>
                            <select class="form-select" name="status" id="team_status" required>
                                <?php $__currentLoopData = \App\Enums\ActiveStatusEnum::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->value); ?>"
                                    <?php echo e($status->value === \App\Enums\ActiveStatusEnum::ACTIVE->value ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($status->value)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">Please select team status.</div>
                        </div>
                    </div>

                    <div class="modal-footer mt-4 px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="assignTeamBtn">
                            <i class="fas fa-save me-1"></i> Assign Team
                        </button>
                    </div>
                </form>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .user-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease-in-out;
        overflow: hidden;
        height: 100%;
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        border-color: #007bff;
    }

    .user-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #007bff;
        background: #fff;
    }

    .user-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .user-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .user-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .user-actions .btn {
        margin: 2px;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
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

    .status-suspended {
        background-color: #fff3cd;
        color: #856404;
    }

    .role-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
    }

    .role-admin {
        background-color: #dc3545;
        color: white;
    }

    .role-surveyor {
        background-color: #0d6efd;
        color: white;
    }

    .role-user {
        background-color: #6c757d;
        color: white;
    }

    .role-manager {
        background-color: #198754;
        color: white;
    }

    .role-team_leader {
        background-color: #fd7e14;
        color: white;
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

    .btn-tracking {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }

    .btn-tracking:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
        color: white;
        transform: translateY(-1px);
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
        let currentDeleteId = null;

        // Load user data on page load
        function loadUserData() {
            $.ajax({
                url: '<?php echo e(route("admin.users.data")); ?>',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#userDataContainer').html(`
                        <div class="col-12 text-center text-muted py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading user data...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    if (response.success) {
                        renderUserCards(response.users);
                    } else {
                        showToast('error', 'Error!', 'Failed to load user data.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading user data:', error);
                    $('#userDataContainer').html(`
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>Failed to Load Data</h5>
                                <p>Unable to load user data. Please try again later.</p>
                                <button class="btn btn-primary mt-2" onclick="location.reload()">
                                    <i class="fas fa-redo me-1"></i> Retry
                                </button>
                            </div>
                        </div>
                    `);
                }
            });
        }

        // Function to render user cards with tracking button
        function renderUserCards(users) {
            let container = $('#userDataContainer');
            container.empty();

            if (!users || users.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h4>No Users Found</h4>
                            <p>Get started by adding your first user to the system.</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus me-1"></i> Add User
                            </button>
                        </div>
                    </div>
                `);
                return;
            }

            users.forEach(user => {
                const profileUrl = user.profile ? `<?php echo e(asset('')); ?>${user.profile}` : '';
                const statusClass = `status-${user.status}`;
                const roleClass = `role-${user.role}`;
                const joinDate = new Date(user.created_at).toLocaleDateString();

                // Show assign button only for team leaders
                const showAssignButton = user.role === 'team_leader';
                // Show tracking button for surveyors and team leaders
                const showTrackingButton = user.role === 'surveyor' || user.role === 'team_leader';

                container.append(`
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="user-card text-center p-4">
                            <div class="mb-3">
                                ${profileUrl
                                    ? `<img src="${profileUrl}" alt="${user.name} Profile" class="user-image">`
                                    : `<div class="user-image bg-light d-inline-flex align-items-center justify-content-center text-secondary">
                                        <i class="fas fa-user fa-2x"></i>
                                    </div>`
                                }
                            </div>
                            <h5 class="user-name">${escapeHtml(user.name)}</h5>
                            <p class="user-meta mb-1"><i class="fas fa-envelope me-1"></i>${escapeHtml(user.email)}</p>
                            <p class="user-meta mb-1">
                                <span class="role-badge ${roleClass}">${user.role.toUpperCase().replace('_', ' ')}</span>
                            </p>
                            <p class="user-meta mb-2">
                                <span class="status-badge ${statusClass}">${user.status.toUpperCase()}</span>
                            </p>

                            ${user.phone ? `<p class="user-meta mb-1"><i class="fas fa-phone me-1"></i>${escapeHtml(user.phone)}</p>` : ''}
                            ${user.city ? `<p class="user-meta mb-1"><i class="fas fa-city me-1"></i>${escapeHtml(user.city)}</p>` : ''}
                            ${user.gender ? `<p class="user-meta mb-1"><i class="fas fa-venus-mars me-1"></i>${user.gender.charAt(0).toUpperCase() + user.gender.slice(1)}</p>` : ''}

                            <div class="user-meta small text-muted mt-2">
                                <i class="fas fa-calendar me-1"></i>Joined: ${joinDate}
                            </div>

                            <div class="user-actions">
                                <button class="btn btn-sm btn-outline-success update-user"
                                        data-id="${user.id}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-user"
                                        data-id="${user.id}"
                                        data-name="${escapeHtml(user.name)}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                                ${showTrackingButton ? `
                                <button class="btn btn-sm btn-tracking view-tracking"
                                        data-id="${user.id}"
                                        data-name="${escapeHtml(user.name)}"
                                        data-role="${user.role}">
                                    <i class="fas fa-map-marked-alt me-1"></i> Track
                                </button>` : ''}
                                ${showAssignButton ? `
                                <button class="btn btn-sm btn-outline-primary assign-user"
                                        data-id="${user.id}"
                                        data-name="${escapeHtml(user.name)}">
                                    <i class="fas fa-user-plus me-1"></i> Assign
                                </button>` : ''}
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // View Tracking button click handler
        $(document).on('click', '.view-tracking', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const userRole = $(this).data('role');

            // Open tracking page in new tab
            window.open(`/admin/tracking/map-view?user_id=${userId}&name=${encodeURIComponent(userName)}&role=${userRole}`, '_blank');
        });

        // Add user form submission
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            const submitBtn = $('#submitBtn');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            let formData = new FormData(this);

            $.ajax({
                url: '<?php echo e(route("admin.users.store")); ?>',
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
                        $('#addUserModal').modal('hide');
                        $('#userForm')[0].reset();
                        loadUserData();
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

        // Update user form submission
        $('#updateUserForm').on('submit', function(e) {
            e.preventDefault();
            const submitBtn = $('#updateSubmitBtn');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...');

            let id = $('#update_id').val();
            let formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: `/admin/users/${id}`,
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
                        $('#updateUserModal').modal('hide');
                        loadUserData();
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
        $(document).on('click', '.update-user', function() {
            let id = $(this).data('id');

            $.ajax({
                url: `/admin/users/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#updateSubmitBtn').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        const user = response.user;

                        $('#update_id').val(user.id);
                        $('#update_name').val(user.name);
                        $('#update_email').val(user.email);
                        $('#update_role').val(user.role);
                        $('#update_status').val(user.status);
                        $('#update_phone').val(user.phone || '');
                        $('#update_city').val(user.city || '');
                        $('#update_gender').val(user.gender || '');
                        $('#update_date_of_birth').val(user.date_of_birth || '');
                        $('#update_password').val('');
                        $('#update_password_confirmation').val('');

                        // Show current profile image if exists
                        if (user.profile) {
                            $('#updateProfilePreview').html(`
                                <img src="<?php echo e(asset('uploads')); ?>/${user.profile}" class="mt-2" width="100" height="100"
                                     style="border-radius:10px;object-fit:cover;border:2px solid #28a745;">
                                <p class="small text-muted mt-1">Current profile image</p>
                            `);
                        } else {
                            $('#updateProfilePreview').html(`
                                <p class="text-muted small mt-2">
                                    <i class="fas fa-user me-1"></i>No profile image available
                                </p>
                            `);
                        }

                        $('#updateUserModal').modal('show');
                    } else {
                        showToast('error', 'Error!', 'Failed to fetch user details.');
                    }
                },
                error: function() {
                    showToast('error', 'Error!', 'Failed to fetch user details.');
                },
                complete: function() {
                    $('#updateSubmitBtn').prop('disabled', false);
                }
            });
        });

        // Handle delete button click
        $(document).on('click', '.delete-user', function() {
            currentDeleteId = $(this).data('id');
            const userName = $(this).data('name');
            $('#userNameToDelete').text(userName);
            $('#deleteConfirmationModal').modal('show');
        });

        // Confirm deletion
        $('#confirmDeleteBtn').on('click', function() {
            if (!currentDeleteId) return;

            const deleteBtn = $(this);
            const originalText = deleteBtn.html();

            deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

            $.ajax({
                url: `/admin/users/${currentDeleteId}`,
                type: 'DELETE',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Deleted!', response.message);
                        $('#deleteConfirmationModal').modal('hide');
                        loadUserData();
                    } else {
                        showToast('error', 'Error!', response.message || 'Failed to delete user.');
                    }
                },
                error: function(xhr) {
                    showToast('error', 'Error!', xhr.responseJSON?.message || 'Failed to delete user.');
                },
                complete: function() {
                    deleteBtn.prop('disabled', false).html(originalText);
                    currentDeleteId = null;
                }
            });
        });

        // Handle assign team button click
        $(document).on('click', '.assign-user', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');

            $('#assign_team_leader_id').val(userId);
            $('#assign_leader_name').val(userName);
            $('#assignUserNameText').text(userName);

            // Pre-fill contact number if available
            const userCard = $(this).closest('.user-card');
            const phoneText = userCard.find('.fa-phone').parent().text().trim();
            if (phoneText) {
                $('#contact_number').val(phoneText);
            }

            // Load corporations
            loadCorporations();

            $('#assignTeamModal').modal('show');
        });

        // Load corporations
        function loadCorporations() {
            $.ajax({
                url: '<?php echo e(route("admin.corporation-list")); ?>',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#corporation_id').prop('disabled', true).html('<option value="">Loading corporations...</option>');
                },
                success: function(response) {
                    let corporations = [];

                    if (response && response.success && response.corporations) {
                        corporations = response.corporations;
                    } else if (response && Array.isArray(response)) {
                        corporations = response;
                    } else if (response && response.corporations) {
                        corporations = response.corporations;
                    }

                    if (corporations && corporations.length > 0) {
                        let options = '<option value="">Select Corporation</option>';
                        corporations.forEach(corp => {
                            if (corp && corp.id && corp.name) {
                                options += `<option value="${corp.id}">${corp.name}</option>`;
                            }
                        });
                        $('#corporation_id').html(options).prop('disabled', false);
                    } else {
                        $('#corporation_id').html('<option value="">No corporations available</option>');
                        showToast('error', 'Error!', 'No corporations found. Please add corporations first.');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading corporations:', xhr);
                    $('#corporation_id').html('<option value="">Error loading corporations</option>');
                    showToast('error', 'Error!', 'Failed to load corporations.');
                }
            });
        }

        // Load wards when corporation is selected
        $(document).on('change', '#corporation_id', function() {
            const corporationId = $(this).val();

            if (!corporationId) {
                $('#ward_id').prop('disabled', true).html('<option value="">Select Ward</option>');
                return;
            }

            $.ajax({
                url: `/admin/wards/${corporationId}/data`,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#ward_id').prop('disabled', true).html('<option value="">Loading wards...</option>');
                },
                success: function(response) {
                    if (response.success && response.wards && response.wards.length > 0) {
                        let options = '<option value="">Select Ward</option>';
                        response.wards.forEach(ward => {
                            const wardDisplay = `Ward ${ward.ward_no}`;
                            options += `<option value="${ward.id}">${wardDisplay}</option>`;
                        });
                        $('#ward_id').html(options).prop('disabled', false);
                    } else {
                        $('#ward_id').html('<option value="">No wards available</option>');
                        showToast('error', 'Error!', 'No wards found for selected corporation.');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading wards:', xhr);
                    $('#ward_id').html('<option value="">Error loading wards</option>');
                    showToast('error', 'Error!', 'Failed to load wards.');
                }
            });
        });

        // Assign team form submission
        $('#assignTeamForm').on('submit', function(e) {
            e.preventDefault();
            const submitBtn = $('#assignTeamBtn');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Assigning...');

            const formData = $(this).serialize();

            $.ajax({
                url: '<?php echo e(route("admin.teams.store")); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    clearValidationErrors();
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Success!', response.message);
                        $('#assignTeamModal').modal('hide');
                        $('#assignTeamForm')[0].reset();
                        loadUserData();
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

            const toastInstance = new bootstrap.Toast(toast[0]);
            toastInstance.show();
        }

        // Reset forms when modals are closed
        $('.modal').on('hidden.bs.modal', function() {
            clearValidationErrors();
            $('.modal form').trigger('reset');
            $('#updateProfilePreview').html('');
        });

        // Initialize page
        loadUserData();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\testinggayu\resources\views/admin/users.blade.php ENDPATH**/ ?>