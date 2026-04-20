@extends('layouts.surveyor-layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header Section -->
                <div class="dashboard-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Team Leader Dashboard</h3>
                            <p class="text-muted mb-0">Manage and monitor your survey teams</p>
                        </div>
                        <div class="dashboard-stats">
                            <span class="badge bg-primary px-3 py-2">
                                <i class="fas fa-users me-1"></i> {{ count($teams) }} Team(s)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- AJAX Alert Container -->
                <div id="ajaxAlertContainer"></div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-dashboard" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-dashboard" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Teams Grid - 2 cards per row -->
                <div class="row">
                    @forelse ($teams as $team)
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4">
                            <!-- Team Card -->
                            <div class="card team-card h-100 shadow-hover">
                                <div class="card-header team-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="team-icon me-3">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-0 text-white">{{ $team->name }}</h5>
                                                <small class="text-white opacity-75">Team ID: #{{ $team->id }}</small>
                                            </div>
                                        </div>
                                        <div class="team-actions">
                                            <span
                                                class="badge status-badge bg-{{ isset($team->status) && $team->status->value == 'active' ? 'success' : 'warning' }} me-2">
                                                <i
                                                    class="fas fa-circle me-1 small-icon"></i>{{ isset($team->status) ? ucfirst($team->status->value) : 'Active' }}
                                            </span>
                                            <button class="btn btn-light btn-sm add-surveyor-btn"
                                                data-team-id="{{ $team->id }}" data-team-name="{{ $team->name }}">
                                                <i class="fas fa-user-plus me-1"></i>Add Surveyor
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Quick Stats -->
                                    <div class="row quick-stats mb-4">
                                        <div class="col-md-3 col-6">
                                            <div class="stat-card text-center p-3 rounded">
                                                <div class="stat-icon mb-2">
                                                    <i class="fas fa-user-friends text-primary"></i>
                                                </div>
                                                <h4 class="mb-1 team-surveyors-count">{{ count($team->members) }}</h4>
                                                <small class="text-muted">Surveyors</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="stat-card text-center p-3 rounded">
                                                <div class="stat-icon mb-2">
                                                    <i class="fas fa-map-marker-alt text-success"></i>
                                                </div>
                                                <h4 class="mb-1">{{ $team->ward->ward_no ?? 'N/A' }}</h4>
                                                <small class="text-muted">Ward No</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="stat-card text-center p-3 rounded">
                                                <div class="stat-icon mb-2">
                                                    <i class="fas fa-building text-info"></i>
                                                </div>
                                                <h4 class="mb-1">{{ Str::limit($team->corporation->name ?? 'N/A', 15) }}
                                                </h4>
                                                <small class="text-muted">Corporation</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="stat-card text-center p-3 rounded">
                                                <div class="stat-icon mb-2">
                                                    <i class="fas fa-phone-alt text-warning"></i>
                                                </div>
                                                <h4 class="mb-1">{{ $team->contact_number }}</h4>
                                                <small class="text-muted">Contact</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Team Details -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="info-section">
                                                <h6 class="section-title">
                                                    <i class="fas fa-info-circle me-2 text-primary"></i>Team Info
                                                </h6>
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Team Leader:</span>
                                                        <span class="info-value">{{ $team->leader_name }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Contact:</span>
                                                        <span class="info-value">{{ $team->contact_number }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Status:</span>
                                                        <span class="info-value">
                                                            <span
                                                                class="badge bg-{{ $team->status == 'active' ? 'success' : 'warning' }}">
                                                                {{ $team->status ?? 'Active' }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Created:</span>
                                                        <span
                                                            class="info-value">{{ \Carbon\Carbon::parse($team->created_at)->format('M d, Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location Details -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="info-section">
                                                <h6 class="section-title">
                                                    <i class="fas fa-map-marked-alt me-2 text-success"></i>Location
                                                </h6>
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Ward No:</span>
                                                        <span class="info-value">{{ $team->ward->ward_no ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Zone:</span>
                                                        <span class="info-value">{{ $team->ward->zone ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Corporation:</span>
                                                        <span
                                                            class="info-value">{{ $team->corporation->name ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assigned Roads Section -->
                                    <div class="roadnames-section mt-2">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="section-title mb-0">
                                                <i class="fas fa-road me-2 text-success"></i>Assigned Roads
                                                <span
                                                    class="badge bg-secondary ms-2">{{ count($roadnames[$team->id] ?? []) }}</span>
                                            </h6>
                                        </div>
                                        @if (!empty($roadnames[$team->id]) && count($roadnames[$team->id]) > 0)
                                            <form class="road-form" data-team-id="{{ $team->id }}">
                                                <div class="input-group">
                                                    <select name="select_roadname"
                                                        id="select_roadname-{{ $team->id }}" class="form-select">
                                                        <option value="">Select a road</option>
                                                        @foreach ($roadnames[$team->id] as $roadName)
                                                            <option value="{{ $roadName }}">{{ $roadName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-check"></i> Missing Bill
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> No roads available for assignment.
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Team Surveyors Section -->
                                    <div class="surveyors-section mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="section-title mb-0">
                                                <i class="fas fa-user-friends me-2 text-info"></i>Team Surveyors
                                                <span
                                                    class="badge bg-secondary ms-2 team-surveyors-badge">{{ count($team->members) }}</span>
                                            </h6>
                                        </div>

                                        @if ($team->members->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover surveyors-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Surveyor</th>
                                                            <th>Contact</th>
                                                            <th>Role</th>
                                                            <th>Status</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="team-surveyors-{{ $team->id }}">
                                                        @foreach ($team->members as $index => $surveyor)
                                                            <tr class="surveyor-row"
                                                                id="surveyor-row-{{ $surveyor->id }}">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="surveyor-avatar bg-light-primary rounded-circle me-2">
                                                                            <i class="fas fa-user text-primary"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-semibold">{{ $surveyor->name }}
                                                                            </div>
                                                                            <small
                                                                                class="text-muted">{{ $surveyor->email ?? 'N/A' }}</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    {{ $surveyor->phone ?? 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge role-badge bg-{{ $surveyor->pivot->role == 'leader' ? 'primary' : ($surveyor->pivot->role == 'assistant' ? 'info' : 'secondary') }}">
                                                                        {{ ucfirst($surveyor->pivot->role) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge status-badge bg-{{ $surveyor->status == 'active' ? 'success' : 'warning' }}">
                                                                        {{ ucfirst($surveyor->status) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($surveyor->pivot->role !== 'leader')
                                                                        <button
                                                                            class="btn btn-sm btn-outline-danger remove-surveyor-btn"
                                                                            data-surveyor-id="{{ $surveyor->id }}"
                                                                            data-surveyor-name="{{ $surveyor->name }}"
                                                                            data-team-id="{{ $team->id }}"
                                                                            data-team-name="{{ $team->name }}"
                                                                            title="Remove from Team">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="empty-state text-center py-4">
                                                <i class="fas fa-users fa-3x text-muted opacity-50 mb-3"></i>
                                                <p class="text-muted mb-0">No surveyors assigned yet.</p>
                                                <button class="btn btn-sm btn-primary mt-2 add-surveyor-btn"
                                                    data-team-id="{{ $team->id }}"
                                                    data-team-name="{{ $team->name }}">
                                                    <i class="fas fa-user-plus me-1"></i>Add Surveyor
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card empty-card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-users fa-4x text-muted opacity-50 mb-3"></i>
                                    <h4 class="text-muted mb-3">No Teams Found</h4>
                                    <p class="text-muted">You are not assigned as a leader of any team yet.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Surveyor Modal -->
    <div class="modal fade" id="removeSurveyorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Remove <strong id="surveyorName"></strong> from <strong id="teamName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveBtn">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Surveyor Modal -->
    <div class="modal fade" id="addSurveyorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Surveyor to <span id="modalTeamName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="addTeamId">
                    <div class="mb-3">
                        <label class="form-label">Select Surveyor</label>
                        <select class="form-select" id="surveyorSelect" required>
                            <option value="">Choose...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="surveyorRole">
                            <option value="surveyor">Surveyor</option>
                            <option value="assistant">Assistant Leader</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAddBtn">Add</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
        }

        .team-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .team-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.25rem;
        }

        .team-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-stats {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 0.75rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: #f8f9fa;
            border-color: #667eea;
        }

        .stat-icon {
            width: 35px;
            height: 35px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .info-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            height: 100%;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.35rem 0;
            font-size: 0.9rem;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            color: #333;
        }

        .surveyors-table {
            font-size: 0.85rem;
        }

        .surveyor-avatar {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            border-radius: 50%;
        }

        .empty-state {
            background: #f8f9fa;
            border-radius: 10px;
        }

        .alert-dashboard {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1rem;
            }

            .info-item {
                flex-direction: column;
            }

            .info-value {
                margin-top: 0.25rem;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentTeamId, currentSurveyorId, currentSurveyorName, currentTeamName;

            // Helper function to show alert messages
            function showAlert(message, type = 'success') {
                const alertDiv = $(`
                    <div class="alert alert-${type} alert-dismissible fade show alert-dashboard" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                            <span>${message}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                $('#ajaxAlertContainer').html(alertDiv);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alertDiv.alert('close');
                }, 5000);
            }

            // Remove surveyor functionality
            $(document).on('click', '.remove-surveyor-btn', function() {
                currentSurveyorId = $(this).data('surveyor-id');
                currentSurveyorName = $(this).data('surveyor-name');
                currentTeamId = $(this).data('team-id');
                currentTeamName = $(this).data('team-name');

                $('#surveyorName').text(currentSurveyorName);
                $('#teamName').text(currentTeamName);
                $('#removeSurveyorModal').modal('show');
            });

            $('#confirmRemoveBtn').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Removing...');

                $.ajax({
                    url: `/teams/${currentTeamId}/members/${currentSurveyorId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message || 'Surveyor removed successfully',
                                'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert(response.message || 'Error removing surveyor', 'danger');
                            $btn.prop('disabled', false).html('Remove');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Error removing surveyor';
                        showAlert(message, 'danger');
                        $btn.prop('disabled', false).html('Remove');
                    }
                });
            });

            // Add surveyor functionality
            $(document).on('click', '.add-surveyor-btn', function() {
                currentTeamId = $(this).data('team-id');
                currentTeamName = $(this).data('team-name');
                $('#addTeamId').val(currentTeamId);
                $('#modalTeamName').text(currentTeamName);
                loadAvailableSurveyors();
                $('#addSurveyorModal').modal('show');
            });

            function loadAvailableSurveyors() {
                $('#surveyorSelect').html('<option>Loading...</option>');
                $.ajax({
                    url: `/teamleader/teams/${currentTeamId}/available-surveyors`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.surveyors && response.surveyors.length) {
                            let options = '<option value="">Choose surveyor...</option>';
                            response.surveyors.forEach(s => {
                                options +=
                                    `<option value="${s.id}">${s.name} (${s.email || 'No email'})</option>`;
                            });
                            $('#surveyorSelect').html(options);
                        } else {
                            $('#surveyorSelect').html(
                                '<option value="">No available surveyors</option>');
                        }
                    },
                    error: function() {
                        $('#surveyorSelect').html('<option value="">Error loading surveyors</option>');
                        showAlert('Error loading available surveyors', 'danger');
                    }
                });
            }

            $('#confirmAddBtn').on('click', function() {
                const surveyorId = $('#surveyorSelect').val();
                const role = $('#surveyorRole').val();

                if (!surveyorId) {
                    showAlert('Please select a surveyor', 'warning');
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

                $.ajax({
                    url: `/teamleader/teams/${currentTeamId}/add-member`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: surveyorId,
                        role: role
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message || 'Surveyor added successfully',
                                'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert(response.message || 'Error adding surveyor', 'danger');
                            $btn.prop('disabled', false).html('Add');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Error adding surveyor';
                        showAlert(message, 'danger');
                        $btn.prop('disabled', false).html('Add');
                    }
                });
            });

            // Reset modal when closed
            $('#addSurveyorModal').on('hidden.bs.modal', function() {
                $('#surveyorSelect').html('<option value="">Choose...</option>');
                $('#surveyorRole').val('surveyor');
                $('#confirmAddBtn').prop('disabled', false).html('Add');
            });

            $('#removeSurveyorModal').on('hidden.bs.modal', function() {
                $('#confirmRemoveBtn').prop('disabled', false).html('Remove');
            });

            // Road assignment functionality for all teams
            $(document).on('submit', '.road-form', function(e) {
                e.preventDefault();

                const teamId = $(this).data('team-id');
                const selectedRoad = $(`#select_roadname-${teamId}`).val();

                $.ajax({
                    url: '/teamleader/teams/missing-bill-download',
                    type: 'POST',
                    xhrFields: {
                        responseType: 'blob' // 🔥 VERY IMPORTANT
                    },
                    data: {
                        _token: '{{ csrf_token() }}',
                        team_id: teamId,
                        road_name: selectedRoad
                    },
                    success: function(data) {

                        const blob = new Blob([data], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });

                        const url = window.URL.createObjectURL(blob);

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'missing_bills.xlsx';
                        document.body.appendChild(a);
                        a.click();

                        a.remove();
                    },
                    error: function() {
                        alert('Download failed');
                    }
                });
            });

        });
    </script>
@endsection
