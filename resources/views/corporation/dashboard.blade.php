@extends('layouts.commissioner')

@section('title', 'Dashboard - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

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

        <!-- First Row - Basic Stats -->
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
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_buildings }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Across all wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Surveyed Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_surveyed }}</h2>
                        <small class="text-info"><i class="fas fa-check-circle"></i> GIS Mapped</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-draw-polygon"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Records</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $mis_count }}</h2>
                        <small class="text-primary"><i class="fas fa-database"></i> Total entries</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>
        </div>

        <!-- Second Row - Variation Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center" style="border-left: 4px solid #ffc107;">
                    <div>
                        <h6 class="text-muted mb-1">Area Variation Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_area_variation_buildings }}</h2>
                        <small class="text-warning">
                            <i class="fas fa-chart-line"></i>
                            {{ $area_variation_percentage }}% of total buildings
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #fff3cd;"><i class="fas fa-arrows-alt" style="color:#ffc107;"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center" style="border-left: 4px solid #17a2b8;">
                    <div>
                        <h6 class="text-muted mb-1">Usage Variation Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_usage_variation_buildings }}</h2>
                        <small class="text-info">
                            <i class="fas fa-chart-line"></i>
                            {{ $usage_variation_percentage }}% of total buildings
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #d1ecf1;"><i class="fas fa-exchange-alt" style="color:#17a2b8;"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center" style="border-left: 4px solid #dc3545;">
                    <div>
                        <h6 class="text-muted mb-1">Both Variations</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_both_variation_buildings }}</h2>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Area + Usage mismatch
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #f8d7da;"><i class="fas fa-bell" style="color:#dc3545;"></i></div>
                </div>
            </div>
        </div>

        <!-- Ward-wise Statistics Table -->
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
                                    <th>Roads</th>
                                    <th>MIS Count</th>
                                    <th>Area Variation</th>
                                    <th>Usage Variation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collections as $data)
                                <tr>
                                    <td>{{ ucfirst($data['zone']) }}</td>
                                    <td>{{ $data['ward_no'] }}</td>
                                    <td>{{ $data['buildingCount'] }}</td>
                                    <td>{{ $data['surveyedBuildingCount'] }}</td>
                                    <td>{{ $data['roadCount'] }}</td>
                                    <td>{{ $data['misCount'] }}</td>
                                    <td>
                                        <span class="badge {{ $data['areaVariationCount'] > 0 ? 'bg-warning' : 'bg-success' }}">
                                            {{ $data['areaVariationCount'] }}
                                            @if($data['buildingCount'] > 0)
                                            ({{ $data['areaVariationPercentage'] }}%)
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $data['usageVariationCount'] > 0 ? 'bg-info' : 'bg-success' }}">
                                            {{ $data['usageVariationCount'] }}
                                            @if($data['buildingCount'] > 0)
                                            ({{ $data['usageVariationPercentage'] }}%)
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('corporation.ward.map', $data['ward_no']) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-map-marked-alt"></i> View Map
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-info variation-details-btn"
                                                    data-ward="{{ $data['ward_no'] }}"
                                                    data-area-variation="{{ $data['areaVariationCount'] }}"
                                                    data-usage-variation="{{ $data['usageVariationCount'] }}"
                                                    data-both-variation="{{ $data['bothVariationCount'] ?? 0 }}"
                                                    data-total-buildings="{{ $data['buildingCount'] }}">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No ward data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variation Details Modal -->
<div class="modal fade" id="variationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #102C57, #1679AB);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-chart-line me-2"></i> Variation Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="variationDetailsContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.variation-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const ward = this.dataset.ward;
            const areaVariation = parseInt(this.dataset.areaVariation);
            const usageVariation = parseInt(this.dataset.usageVariation);
            const bothVariation = parseInt(this.dataset.bothVariation);
            const totalBuildings = parseInt(this.dataset.totalBuildings);

            const areaPercentage = totalBuildings > 0 ? ((areaVariation / totalBuildings) * 100).toFixed(1) : 0;
            const usagePercentage = totalBuildings > 0 ? ((usageVariation / totalBuildings) * 100).toFixed(1) : 0;
            const bothPercentage = totalBuildings > 0 ? ((bothVariation / totalBuildings) * 100).toFixed(1) : 0;

            const modalContent = `
                <div class="text-center mb-4">
                    <h4 class="fw-bold" style="color:#102C57;">Ward ${ward}</h4>
                    <p class="text-muted">Total Buildings: <strong>${totalBuildings}</strong></p>
                </div>

                <div class="progress mb-4" style="height: 30px;">
                    <div class="progress-bar bg-warning" style="width: ${areaPercentage}%" role="progressbar">
                        Area Variation: ${areaVariation} (${areaPercentage}%)
                    </div>
                    <div class="progress-bar bg-info" style="width: ${usagePercentage}%" role="progressbar">
                        Usage Variation: ${usageVariation} (${usagePercentage}%)
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="alert alert-warning d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-arrows-alt me-2"></i> Area Variation</span>
                            <span class="badge bg-warning rounded-pill">${areaVariation}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-exchange-alt me-2"></i> Usage Variation</span>
                            <span class="badge bg-info rounded-pill">${usageVariation}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-danger d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-exclamation-triangle me-2"></i> Both Variations</span>
                            <span class="badge bg-danger rounded-pill">${bothVariation}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Area variation indicates mismatch between polygon area and assessment area.
                        Usage variation indicates mismatch between building usage and assessment usage.
                    </small>
                </div>
            `;

            document.getElementById('variationDetailsContent').innerHTML = modalContent;
            new bootstrap.Modal(document.getElementById('variationModal')).show();
        });
    });
</script>
@endpush
