@extends('layouts.surveyor-layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header Section -->
                <div class="dashboard-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Surveyor Dashboard</h3>
                            <p class="text-muted mb-0">Your team information and assignments</p>
                        </div>
                        <div class="dashboard-stats">
                            @if($hasTeam)
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="fas fa-users me-1"></i> {{ $team->name }}
                                </span>
                            @else
                                <span class="badge bg-warning px-3 py-2">
                                    <i class="fas fa-user-times me-1"></i> No Team Assigned
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($hasTeam)
                    <!-- Team Card -->
                    <div class="card team-card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>Team Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Team Details -->
                                <div class="col-md-6">
                                    <h6>Team Details</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Team Name:</strong></td>
                                            <td>{{ $team->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Team Leader:</strong></td>
                                            <td>{{ $team->leader_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact:</strong></td>
                                            <td>{{ $team->contact_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Your Role:</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ ($userRole) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $team->status == 'active' ? 'success' : 'warning' }}">
                                                    {{ ($team->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Ward Details -->
                                <div class="col-md-6">
                                    <h6>Assigned Ward Details</h6>
                                    @if($ward)
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Ward No:</strong></td>
                                                <td>{{ $ward->ward_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Zone:</strong></td>
                                                <td>{{ $ward->zone ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Area:</strong></td>
                                                <td>{{ $ward->area ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Population:</strong></td>
                                                <td>{{ $ward->population ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Households:</strong></td>
                                                <td>{{ $ward->households ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            No ward assigned to this team.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Corporation Details -->
                            @if($corporation)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6>Corporation Information</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Name:</strong> {{ $corporation->name ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>District:</strong> {{ $corporation->district ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>State:</strong> {{ $corporation->state ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-map-marked-alt fa-2x text-primary mb-3"></i>
                                    <h5>Survey Area</h5>
                                    <p class="text-muted">View assigned survey locations</p>
                                    <a class="btn btn-outline-primary" href="{{route('surveyor.mapview')}}">View Map</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-tasks fa-2x text-success mb-3"></i>
                                    <h5>My Tasks</h5>
                                    <p class="text-muted">Check assigned surveys</p>
                                    <button class="btn btn-outline-success">View Tasks</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-chart-bar fa-2x text-info mb-3"></i>
                                    <h5>Progress</h5>
                                    <p class="text-muted">Track survey progress</p>
                                    <a class="btn btn-outline-info" href="{{ route('surveyor.progress') }}">View Progress</a>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- No Team Content -->
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Team Assigned</h4>
                                <p class="text-muted">{{ $message }}</p>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please contact your team leader or administrator to be assigned to a team.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .dashboard-header h3 {
            color: white;
            font-weight: 700;
        }
        .team-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table td {
            border: none;
            padding: 8px 0;
        }
        .table tr {
            border-bottom: 1px solid #e9ecef;
        }
        .table tr:last-child {
            border-bottom: none;
        }
    </style>
@endsection
