{{-- resources/views/corporation/dashboard.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Dashboard - Tamil Nadu Municipal Corporation')

@section('content')
<div class="dashboard-content-area">
    <div class="animate__animated animate__fadeInUp">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold" style="color:#ffffff;">
                <i class="fas fa-tachometer-alt me-2" style="color:#1679AB;"></i>
                Dashboard Overview
            </h3>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $ward_count }}</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> Active wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ array_sum(array_column($collections, 'buildingCount')) }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Across all wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Surveyed Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ array_sum(array_column($collections, 'surveyedBuildingCount')) }}</h2>
                        <small class="text-info"><i class="fas fa-check-circle"></i> GIS Mapped</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-draw-polygon"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Road Networks</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ array_sum(array_column($collections, 'roadCount')) }}</h2>
                        <small class="text-success"><i class="fas fa-road"></i> Total road segments</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-road"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>
                        Ward-wise Statistics
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Zone</th>
                                    <th>Ward No</th>
                                    <th>Buildings</th>
                                    <th>Surveyed</th>
                                    <th>Points</th>
                                    <th>Roads</th>
                                    <th>MIS Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collections as $data)
                                <tr>
                                    <td>{{ ucfirst($data['zone']) }}</td>
                                    <td>{{ $data['ward_no'] }}</td>
                                    <td>{{ $data['buildingCount'] }}</td>
                                    <td>{{ $data['surveyedBuildingCount'] }}</td>
                                    <td>{{ $data['pointCount'] }}</td>
                                    <td>{{ $data['roadCount'] }}</td>
                                    <td>{{ $data['misCount'] }}</td>
                                    <td>
                                        <a href="{{ route('corporation.ward.details', $data['ward_no']) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-map-marked-alt"></i> View Map
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
