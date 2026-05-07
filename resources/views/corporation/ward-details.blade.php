@extends('layouts.commissioner')

@section('title', 'Ward Details - Ward ' . ($ward->ward_no ?? ''))

@section('content-panels')
<div class="content-panel" style="display: block;">
    <div class="animate__animated animate__fadeInUp">
        <!-- Header with Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('corporation.dashboard') }}" class="btn btn-outline-light mb-2">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
                <h3 class="fw-bold text-white mt-2">
                    <i class="fas fa-map-marker-alt me-2" style="color:#1679AB;"></i>
                    Ward {{ $ward->ward_no }} Details
                </h3>
                <p class="text-white-50 mb-0">Zone: {{ $ward->zone }} | Corporation: {{ $corporation->name ?? '' }}</p>
            </div>
            <div class="stat-card p-3 text-center" style="min-width: 150px;">
                <h5 class="mb-0">{{ $totalBuildings ?? 0 }}</h5>
                <small class="text-muted">Total Buildings</small>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 text-center">
                    <div class="stat-icon mx-auto mb-2"><i class="fas fa-building"></i></div>
                    <h3 class="fw-bold mb-0">{{ $totalBuildings ?? 0 }}</h3>
                    <small class="text-muted">Total Buildings</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 text-center">
                    <div class="stat-icon mx-auto mb-2"><i class="fas fa-check-circle"></i></div>
                    <h3 class="fw-bold mb-0">{{ $gisIdCount ?? 0 }}</h3>
                    <small class="text-muted">GIS ID Assigned</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 text-center">
                    <div class="stat-icon mx-auto mb-2"><i class="fas fa-road"></i></div>
                    <h3 class="fw-bold mb-0">{{ $totalRoads ?? 0 }}</h3>
                    <small class="text-muted">Road Networks</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 text-center">
                    <div class="stat-icon mx-auto mb-2"><i class="fas fa-map-pin"></i></div>
                    <h3 class="fw-bold mb-0">{{ $totalPoints ?? 0 }}</h3>
                    <small class="text-muted">Point Data</small>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="stat-card p-4 mb-4">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-map me-2" style="color:#1679AB;"></i>
                Ward Map Visualization
            </h5>
            <div id="map" style="height: 500px; background: #f0f0f0; border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                <div class="text-center text-muted">
                    <i class="fas fa-map-marked-alt fa-4x mb-3"></i>
                    <p>Map integration ready. Load GIS data for visualization.</p>
                    <small>Polygons: {{ count($polygons ?? []) }} | Roads: {{ count($roads ?? []) }} | Points: {{ count($points ?? []) }}</small>
                </div>
            </div>
        </div>

        <!-- Data Tables -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-draw-polygon me-2" style="color:#1679AB;"></i>
                        Buildings (Polygons)
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>GIS ID</th><th>Owner Name</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse(($polygons ?? []) as $polygon)
                                <tr>
                                    <td>{{ $polygon->gisid ?? 'N/A' }}</td>
                                    <td>{{ $polygon->owner_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">Mapped</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">No building data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-road me-2" style="color:#1679AB;"></i>
                        Roads
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Road Name</th><th>Type</th></tr></thead>
                            <tbody>
                                @forelse(($roads ?? []) as $road)
                                <tr>
                                    <td>{{ $road->road_name ?? 'Unnamed Road' }}</td>
                                    <td><span class="badge bg-info">Line String</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center">No road data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // You can integrate Leaflet or Google Maps here
    console.log('Ward {{ $ward->ward_no }} - GIS Data Ready');
</script>
@endpush
@endsection
