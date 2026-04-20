@extends('layouts.admin-layout')
@section('title', 'Team Management')

@section('content')
    <div class="container-fluid py-3">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold text-primary"><i class="fas fa-users me-2"></i>Team Management</h1>
                <p class="text-muted mb-0">Manage all teams and their members - One surveyor per team only</p>
            </div>
        </div>

        <!-- Teams Container -->
        <div class="row g-4" id="teamsContainer">
            <div class="col-12 text-center text-muted py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading teams data...</p>
            </div>
        </div>
    </div>

    <!-- Team Details Modal -->
    <div class="modal fade" id="teamDetailsModal" tabindex="-1" aria-labelledby="teamDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="teamDetailsModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Team Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Team Information</h6>
                            <p><strong>Name:</strong> <span id="detailTeamName"></span></p>
                            <p><strong>Leader:</strong> <span id="detailTeamLeader"></span></p>
                            <p><strong>Contact:</strong> <span id="detailTeamContact"></span></p>
                            <p><strong>Status:</strong> <span id="detailTeamStatus"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Location</h6>
                            <p><strong>Ward:</strong> <span id="detailTeamWard"></span></p>
                            <p><strong>Corporation:</strong> <span id="detailTeamCorporation"></span></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Team Members</h6>
                            <small class="text-muted">One surveyor per team only</small>
                        </div>
                        <div id="teamMembersList" class="mt-2">
                            <!-- Members will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addMemberModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Add Team Member
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Each surveyor can only be assigned to one team.
                    </div>
                    <form id="addMemberForm">
                        @csrf
                        <input type="hidden" name="team_id" id="addMemberTeamId">

                        <div class="mb-3">
                            <label class="form-label fw-bold required">Select Surveyor</label>
                            <select class="form-select" name="user_id" id="memberUserId" required>
                                <option value="">Select Surveyor</option>
                            </select>
                            <div class="form-text">Only available (unassigned) surveyors are shown</div>
                            <div class="invalid-feedback">Please select a surveyor.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold required">Role</label>
                            <select class="form-select" name="role" id="memberRole" required>
                                <option value="surveyor">Surveyor</option>
                                <option value="assistant">Assistant</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmAddMember">Add Member</button>
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
        .team-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            height: 100%;
        }

        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            border-color: #007bff;
        }

        .team-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 1rem;
            margin: -1rem -1rem 1rem -1rem;
        }

        .team-name {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .team-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .member-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            display: inline-block;
        }

        .badge-surveyor {
            background-color: #0dcaf0;
            color: #000;
        }

        .badge-assistant {
            background-color: #6c757d;
            color: white;
        }

        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .restriction-badge {
            background-color: #ffc107;
            color: #000;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
        }

        .road-select {
            font-size: 0.85rem;
        }
    </style>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Load teams data
            function loadTeamsData() {
                $.ajax({
                    url: '{{ route('admin.teams.data') }}',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#teamsContainer').html(`
                        <div class="col-12 text-center text-muted py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading teams data...</p>
                        </div>
                    `);
                    },
                    success: function(response) {
                        if (response.success) {
                            renderTeamsCards(response.teams);
                        } else {
                            showToast('error', 'Error!', 'Failed to load teams data.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading teams:', xhr);
                        $('#teamsContainer').html(`
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>Failed to Load Data</h5>
                                <p>Unable to load teams data. Please try again later.</p>
                                <button class="btn btn-primary mt-2" onclick="location.reload()">
                                    <i class="fas fa-redo me-1"></i> Retry
                                </button>
                            </div>
                        </div>
                    `);
                    }
                });
            }

            // Render teams cards with restrictions
            function renderTeamsCards(teams) {
                let container = $('#teamsContainer');
                container.empty();

                if (!teams || teams.length === 0) {
                    container.html(`
                    <div class="col-12">
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h4>No Teams Found</h4>
                            <p class="text-muted">No teams have been created yet.</p>
                        </div>
                    </div>
                `);
                    return;
                }

                teams.forEach(team => {
                    const memberCount = team.members ? team.members.length : 0;
                    const statusClass = team.status === 'active' ? 'text-success' : 'text-danger';
                    const hasMembers = memberCount > 0;
                    const canDelete = !hasMembers;

                    container.append(`
                    <div class="col-xl-4 col-lg-6 col-md-6" data-team-card="${team.id}">
                        <div class="team-card p-4">
                            <div class="team-header">
                                <div class="team-name">${escapeHtml(team.name)}</div>
                                <div class="team-meta text-white-50">
                                    <i class="fas fa-user-shield me-1"></i>${escapeHtml(team.leader_name)}
                                </div>
                            </div>

                            <div class="team-meta">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Ward ${team.ward ? team.ward.ward_no : 'N/A'}
                                ${team.ward && team.ward.corporation ? ` - ${escapeHtml(team.ward.corporation.name)}` : ''}
                            </div>

                            <div class="team-meta">
                                <i class="fas fa-phone me-1"></i>${escapeHtml(team.contact_number)}
                            </div>

                            <div class="team-meta">
                                <i class="fas fa-circle ${statusClass} me-1"></i>
                                ${team.status.toUpperCase()}
                            </div>

                            <div class="team-meta mt-2">
                                <i class="fas fa-users me-1"></i>
                                ${memberCount} Team Member(s)
                                ${hasMembers ? '<span class="restriction-badge ms-2">Cannot Delete</span>' : ''}
                            </div>

                            ${memberCount > 0 ? `
                                <div class="mt-2">
                                    <small class="text-muted">Members:</small><br>
                                    <div id="members-${team.id}">
                                        ${team.members.slice(0, 3).map(member => `
                                        <span class="member-badge badge-${member.pivot.role}">
                                            ${escapeHtml(member.name)}
                                        </span>
                                    `).join('')}
                                        ${memberCount > 3 ? `<span class="text-muted small">+${memberCount - 3} more</span>` : ''}
                                    </div>
                                </div>
                            ` : ''}

                            <!-- Road selection dropdown with unique class and data attribute -->
                            <div class="mt-3">
                                <label class="form-label small fw-bold">Assigned Roads</label>
                                <select class="form-select road-select" data-team-id="${team.id}">
                                    <option value="">Select Road</option>
                                </select>
                            </div>

                            <div class="team-actions mt-3 pt-3 border-top">
                                <button class="btn btn-sm btn-outline-info view-team"
                                        data-id="${team.id}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                                <button class="btn btn-sm btn-outline-success add-member"
                                        data-id="${team.id}"
                                        data-name="${escapeHtml(team.name)}">
                                    <i class="fas fa-user-plus me-1"></i> Add Member
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-team ${!canDelete ? 'btn-disabled' : ''}"
                                        data-id="${team.id}"
                                        data-name="${escapeHtml(team.name)}"
                                        ${!canDelete ? 'disabled' : ''}
                                        title="${!canDelete ? 'Cannot delete team with active members' : 'Delete team'}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                                <button class="btn btn-sm btn-outline-success assign-road-btn"
                                        data-id="${team.id}"
                                        data-name="${escapeHtml(team.name)}">
                                    <i class="fas fa-road me-1"></i> Assign Road
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                    // Load roads for this team's dropdown
                    loadRoadsForTeam(team.id);
                });
            }

            // Load roads for a specific team dropdown
            function loadRoadsForTeam(teamId) {
                // Find the select element within the specific team card
                const $select = $(`.team-card`).closest(`[data-team-card="${teamId}"]`).find('.road-select');

                // Alternative approach - find by data attribute
                const $selectAlt = $(`.road-select[data-team-id="${teamId}"]`);
                const $targetSelect = $select.length ? $select : $selectAlt;

                $.ajax({
                    url: `/admin/teams/${teamId}/load-roads`,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $targetSelect.html('<option value="">Loading roads...</option>');
                    },
                    success: function(response) {
                        if (response.success) {
                            let options = '<option value="">Select Road</option>';

                            if (response.road_name && Array.isArray(response.road_name) && response.road_name.length > 0) {
                                response.road_name.forEach(road => {
                                    options += `<option value="${escapeHtml(road)}">${escapeHtml(road)}</option>`;
                                });
                                $targetSelect.html(options);
                            } else {
                                $targetSelect.html('<option value="">No roads assigned</option>');
                            }
                        } else {
                            $targetSelect.html('<option value="">Error loading roads</option>');
                            console.error('Error response:', response);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading roads for team', teamId, ':', xhr);
                        $targetSelect.html('<option value="">Error loading roads</option>');
                    }
                });
            }

            // Assign road to team - FIXED to get correct dropdown value
            function assignRoadToTeam(teamId, roadName, $button) {
                if (!roadName || roadName === 'Select Road' || roadName === '') {
                    showToast('warning', 'Warning!', 'Please select a valid road first.');
                    return false;
                }

                $.ajax({
                    url: `/admin/teams/assigned-roads`,
                    type: 'POST',
                    data: {
                        team_id: teamId,
                        road_name: roadName,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Assigning...');
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Success!', response.message || 'Road assigned successfully.');
                            // Reload the roads list after assignment
                            loadRoadsForTeam(teamId);
                        } else {
                            showToast('error', 'Error!', response.message || 'Failed to assign road.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error assigning road:', xhr);
                        let errorMsg = 'Failed to assign road.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showToast('error', 'Error!', errorMsg);
                    },
                    complete: function() {
                        $button.prop('disabled', false).html('<i class="fas fa-road me-1"></i> Assign Road');
                    }
                });
            }

            // View team details
            $(document).on('click', '.view-team', function() {
                const teamId = $(this).data('id');

                $.ajax({
                    url: `/admin/teams/${teamId}/edit`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const team = response.team;

                            $('#detailTeamName').text(team.name);
                            $('#detailTeamLeader').text(team.leader_name);
                            $('#detailTeamContact').text(team.contact_number);
                            $('#detailTeamStatus').text(team.status.toUpperCase());
                            $('#detailTeamWard').text(team.ward ? `Ward ${team.ward.ward_no}` : 'N/A');
                            $('#detailTeamCorporation').text(team.ward && team.ward.corporation ? team.ward.corporation.name : 'N/A');

                            // Load members
                            let membersHtml = '';
                            if (team.members && team.members.length > 0) {
                                team.members.forEach(member => {
                                    membersHtml += `
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <strong>${escapeHtml(member.name)}</strong>
                                            <span class="badge badge-${member.pivot.role} ms-2">${member.pivot.role}</span>
                                            <br>
                                            <small class="text-muted">${escapeHtml(member.email)}</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger remove-member"
                                                data-team-id="${team.id}"
                                                data-user-id="${member.id}"
                                                data-user-name="${escapeHtml(member.name)}">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                `;
                                });
                            } else {
                                membersHtml = '<p class="text-muted">No members assigned to this team.</p>';
                            }
                            $('#teamMembersList').html(membersHtml);

                            $('#teamDetailsModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showToast('error', 'Error!', 'Failed to load team details.');
                    }
                });
            });

            // Add member modal
            $(document).on('click', '.add-member', function() {
                const teamId = $(this).data('id');
                const teamName = $(this).data('name');

                $('#addMemberTeamId').val(teamId);
                $('#addMemberModalLabel').html(
                    `<i class="fas fa-user-plus me-2"></i>Add Member to ${escapeHtml(teamName)}`);

                // Load available surveyors
                loadAvailableSurveyors(teamId);

                $('#addMemberModal').modal('show');
            });

            // Assign road button click - FIXED to get correct dropdown for the clicked card
            $(document).on('click', '.assign-road-btn', function() {
                const $button = $(this);
                const teamId = $button.data('id');
                const teamName = $button.data('name');

                // Find the select dropdown within the same team card
                const $teamCard = $button.closest('.team-card');
                const $roadSelect = $teamCard.find('.road-select');
                const selectedRoad = $roadSelect.val();

                console.log('Team ID:', teamId);
                console.log('Selected Road:', selectedRoad);
                console.log('Road Select HTML:', $roadSelect.html());

                if (!selectedRoad || selectedRoad === 'Select Road' || selectedRoad === '') {
                    showToast('warning', 'Warning!', 'Please select a road first.');
                    return;
                }

                if (confirm(`Assign "${selectedRoad}" to team "${teamName}"?`)) {
                    assignRoadToTeam(teamId, selectedRoad, $button);
                }
            });

            // Load available surveyors
            function loadAvailableSurveyors(teamId) {
                $.ajax({
                    url: `/admin/teams/${teamId}/available-surveyors`,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#memberUserId').html('<option value="">Loading available surveyors...</option>');
                    },
                    success: function(response) {
                        if (response.success) {
                            let options = '<option value="">Select Surveyor</option>';
                            if (response.surveyors && response.surveyors.length > 0) {
                                response.surveyors.forEach(surveyor => {
                                    options += `<option value="${surveyor.id}">${escapeHtml(surveyor.name)} (${escapeHtml(surveyor.email)})</option>`;
                                });
                                $('#memberUserId').html(options);
                            } else {
                                $('#memberUserId').html('<option value="">No available surveyors</option>');
                                showToast('info', 'Info', 'No available surveyors found. All surveyors are already assigned to teams.');
                            }
                        } else {
                            $('#memberUserId').html('<option value="">Error loading surveyors</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading surveyors:', xhr);
                        $('#memberUserId').html('<option value="">Error loading surveyors</option>');
                        showToast('error', 'Error!', 'Failed to load surveyors.');
                    }
                });
            }

            // Add member to team
            $('#confirmAddMember').on('click', function() {
                const formData = $('#addMemberForm').serialize();
                const teamId = $('#addMemberTeamId').val();

                $.ajax({
                    url: `/admin/teams/${teamId}/add-member`,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Success!', response.message);
                            $('#addMemberModal').modal('hide');
                            loadTeamsData(); // Reload teams
                        } else {
                            showToast('error', 'Error!', response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Please check the form for errors.';
                            if (errors && errors.user_id) {
                                errorMessage = errors.user_id[0];
                            }
                            showToast('error', 'Validation Error!', errorMessage);
                        } else {
                            showToast('error', 'Error!', 'Failed to add member.');
                        }
                    }
                });
            });

            // Remove member from team
            $(document).on('click', '.remove-member', function() {
                const teamId = $(this).data('team-id');
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');

                if (confirm(`Are you sure you want to remove ${userName} from this team?`)) {
                    $.ajax({
                        url: `/admin/teams/${teamId}/remove-member/${userId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToast('success', 'Success!', response.message);
                                loadTeamsData(); // Reload teams
                                $('#teamDetailsModal').modal('hide');
                            } else {
                                showToast('error', 'Error!', response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            showToast('error', 'Error!', 'Failed to remove member.');
                        }
                    });
                }
            });

            // Delete team with validation
            $(document).on('click', '.delete-team:not(.btn-disabled)', function() {
                const teamId = $(this).data('id');
                const teamName = $(this).data('name');

                if (confirm(`Are you sure you want to delete team "${teamName}"? This action cannot be undone.`)) {
                    $.ajax({
                        url: `/admin/teams/${teamId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToast('success', 'Success!', response.message);
                                loadTeamsData();
                            } else {
                                showToast('error', 'Error!', response.message);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                showToast('error', 'Cannot Delete!', xhr.responseJSON.message);
                            } else {
                                showToast('error', 'Error!', 'Failed to delete team.');
                            }
                        }
                    });
                }
            });

            // Escape HTML to prevent XSS
            function escapeHtml(str) {
                if (!str) return '';
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // Show toast notification
            function showToast(type, title, message) {
                const toast = $('#liveToast');
                toast.removeClass('success error warning info');

                // Set icon based on type
                let icon = 'fas fa-circle me-2';
                if (type === 'success') icon = 'fas fa-check-circle me-2';
                if (type === 'error') icon = 'fas fa-exclamation-circle me-2';
                if (type === 'warning') icon = 'fas fa-exclamation-triangle me-2';
                if (type === 'info') icon = 'fas fa-info-circle me-2';

                toast.find('.toast-icon').attr('class', icon);
                toast.find('.toast-title').text(title);
                toast.find('.toast-message').text(message);

                const toastInstance = new bootstrap.Toast(toast[0], {
                    autohide: true,
                    delay: 3000
                });
                toastInstance.show();
            }

            // Initialize
            loadTeamsData();
        });
    </script>
@endsection
