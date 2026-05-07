@extends('layouts.commissioner')

@section('title', 'Dashboard - Municipal Corporation')

@section('content')

<div class="animate__animated animate__fadeInUp">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h3 class="fw-bold" style="color:#ffffff;">
            <i class="fas fa-tachometer-alt me-2" style="color:#1679AB;"></i> Dashboard Overview
        </h3>
        <button class="btn btn-primary px-4 py-2 shadow-sm" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i> Refresh
        </button>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Wards</h6>
                    <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($ward_count) }}</h2>
                    <small class="text-success"><i class="fas fa-check-circle"></i> Active Wards</small>
                </div>
                <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total MIS Records</h6>
                    <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($mis_count) }}</h2>
                    <small class="text-success"><i class="fas fa-database"></i> Tax Records</small>
                </div>
                <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Active Zones</h6>
                    <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format(count($zones)) }}</h2>
                    <small class="text-info"><i class="fas fa-location-dot"></i> Administrative Zones</small>
                </div>
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Buildings</h6>
                    <h2 class="fw-bold mb-0" style="color:#102C57;">
                        {{ number_format($collections->sum('buildingCount')) }}
                    </h2>
                    <small class="text-info"><i class="fas fa-building"></i> Registered</small>
                </div>
                <div class="stat-icon"><i class="fas fa-city"></i></div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stat-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-bullhorn me-2" style="color:#1679AB;"></i> Recent Activities
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ward</th>
                                <th>Activity</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivities as $activity)
                            <tr>
                                <td>{{ $activity->ward }}</td>
                                <td>{{ $activity->activity }}</td>
                                <td>
                                    <span class="badge bg-{{ $activity->status_type }}">
                                        {{ $activity->status }}
                                    </span>
                                </td>
                                <td>{{ $activity->date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone Performance -->
    <div class="row">
        <div class="col-12">
            <div class="stat-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i> Zone Performance
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Zone</th>
                                <th>Wards</th>
                                <th>Total Buildings</th>
                                <th>Tax Collection (Cr)</th>
                                <th>Grievances Resolved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zonePerformance as $zone)
                            <tr>
                                <td><strong>{{ $zone->zone }}</strong></td>
                                <td>{{ $zone->wards }}</td>
                                <td>{{ number_format($zone->buildings) }}</td>
                                <td>₹{{ $zone->collected }}Cr</td>
                                <td>{{ $zone->resolved }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
