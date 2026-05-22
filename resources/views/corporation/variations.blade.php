{{-- resources/views/variations/index.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Variation Analysis - Ward ' . ($warddetail->ward_no ?? '') . ' | Tamil Nadu Municipal Corporation')

@section('content')

<div class="dashboard-content-area">
    <div class="animate__animated animate__fadeInUp">

        <!-- Header with Back Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ url('/corporation/dashboard') }}" class="text-white text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/corporation/analytics') }}" class="text-white text-decoration-none">Analytics</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Ward {{ $ward_no }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold text-white mb-0">
                    <i class="fas fa-chart-line me-2" style="color:#FFD700;"></i>
                    Variation Analysis - Ward {{ $ward_no }}
                </h3>
                <p class="text-white-50 mt-1 mb-0">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $warddetail->zone ?? 'N/A' }} Zone
                </p>
            </div>
            <div>
                <a href="{{ url('/corporation/ward/' . $ward_no) }}" class="btn btn-light me-2">
                    <i class="fas fa-map-marked-alt me-1"></i> View Map
                </a>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format(count($results)) }}</h2>
                        <small class="text-info"><i class="fas fa-draw-polygon"></i> Polygons analyzed</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Area Excess</h6>
                        <h2 class="fw-bold mb-0" style="color:#dc3545;">
                            {{ number_format($results->where('area_variation', 'EXCESS')->count()) }}
                        </h2>
                        <small class="text-danger"><i class="fas fa-arrow-up"></i> Drone > MIS by 100+ sqft</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-expand-alt text-danger"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Area Short</h6>
                        <h2 class="fw-bold mb-0" style="color:#ffc107;">
                            {{ number_format($results->where('area_variation', 'SHORT')->count()) }}
                        </h2>
                        <small class="text-warning"><i class="fas fa-arrow-down"></i> Drone < MIS by 100+ sqft</small>
                    </div>
                    <div class="stat-icon bg-warning-subtle"><i class="fas fa-compress-alt text-warning"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Usage Mismatch</h6>
                        <h2 class="fw-bold mb-0" style="color:#1679AB;">
                            {{ number_format($results->where('usage_variation', true)->count()) }}
                        </h2>
                        <small class="text-primary"><i class="fas fa-exchange-alt"></i> Buildings with usage variation</small>
                    </div>
                    <div class="stat-icon bg-primary-subtle"><i class="fas fa-store text-primary"></i></div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="stat-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-filter me-2" style="color:#1679AB;"></i>
                    Filter Buildings
                </h5>
                <button class="btn btn-sm btn-outline-secondary clear-filters" id="clearFiltersBtn">
                    <i class="fas fa-undo-alt me-1"></i> Clear All
                </button>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">GIS ID</label>
                    <input type="text" class="form-control" id="filterGisid" placeholder="Enter GIS ID...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Building Name</label>
                    <input type="text" class="form-control" id="filterBuildingName" placeholder="Search building...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Area Variation</label>
                    <select class="form-select" id="filterAreaVariation">
                        <option value="">All</option>
                        <option value="EXCESS">Excess (+100 sqft)</option>
                        <option value="SHORT">Short (-100 sqft)</option>
                        <option value="MATCHED">Matched</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Usage Variation</label>
                    <select class="form-select" id="filterUsageVariation">
                        <option value="">All</option>
                        <option value="true">Has Mismatch</option>
                        <option value="false">No Mismatch</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Min. Area Difference</label>
                    <input type="number" class="form-control" id="filterMinDifference" placeholder="Min sqft">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Max. Area Difference</label>
                    <input type="number" class="form-control" id="filterMaxDifference" placeholder="Max sqft">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="applyFiltersBtn">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Counter -->
        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
            <span class="badge bg-primary p-2">
                <i class="fas fa-chart-bar me-1"></i>
                Showing <span id="resultCount">{{ count($results) }}</span> of {{ count($results) }} buildings
            </span>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-light view-toggle active" data-view="card">
                    <i class="fas fa-th-large"></i> Card View
                </button>
                <button type="button" class="btn btn-sm btn-outline-light view-toggle" data-view="table">
                    <i class="fas fa-table"></i> Table View
                </button>
            </div>
        </div>

        <!-- Card View Container -->
        <div id="cardViewContainer">
            <div class="row g-4" id="buildingsContainer">
                @forelse($results as $building)
                    @php
                        $areaVariationClass = $building['area_variation'] == 'EXCESS' ? 'danger' : ($building['area_variation'] == 'SHORT' ? 'warning' : 'success');
                        $areaVariationIcon = $building['area_variation'] == 'EXCESS' ? 'arrow-up' : ($building['area_variation'] == 'SHORT' ? 'arrow-down' : 'check');
                    @endphp
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 building-card-item"
                        data-gisid="{{ $building['gisid'] }}"
                        data-building-name="{{ strtolower($building['building_name']) }}"
                        data-area-variation="{{ $building['area_variation'] }}"
                        data-usage-variation="{{ $building['usage_variation'] ? 'true' : 'false' }}"
                        data-area-difference="{{ $building['area_difference'] }}">

                        <div class="building-card h-100">
                            <!-- Card Header -->
                            <div class="building-card-header p-3"
                                style="background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-0 text-white fw-bold">
                                            <i class="fas fa-building me-2"></i>
                                            {{ $building['building_name'] ?: 'Unnamed Building' }}
                                        </h5>
                                        <small class="text-white-50">
                                            <i class="fas fa-tag me-1"></i>GIS ID: {{ $building['gisid'] }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $areaVariationClass }}">
                                        <i class="fas fa-{{ $areaVariationIcon }} me-1"></i>
                                        {{ $building['area_variation'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="building-card-body p-3">
                                <!-- Location Info -->
                                <div class="info-section mb-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="info-item p-2 bg-light rounded">
                                                <small class="text-muted d-block">Road Name</small>
                                                <strong>{{ $building['road_name'] ?: 'N/A' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-item p-2 bg-light rounded">
                                                <small class="text-muted d-block">Building Usage</small>
                                                <strong>{{ ucfirst($building['building_usage'] ?: 'N/A') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Building Specifications -->
                                <div class="info-section mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-calculator me-2" style="color:#1679AB;"></i>
                                        Building Specifications
                                    </h6>
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Floors</small>
                                                <strong class="fs-5">{{ $building['number_floor'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Basement</small>
                                                <strong class="fs-5">{{ $building['basement'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">%</small>
                                                <strong class="fs-5">{{ $building['percentage'] }}%</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Area Variation Section -->
                                <div class="info-section mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-chart-area me-2" style="color:#28a745;"></i>
                                        Area Analysis (sqft)
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="info-item p-2 {{ $building['area_difference'] > 0 ? 'bg-danger bg-opacity-10' : ($building['area_difference'] < 0 ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10') }} rounded">
                                                <small class="text-muted d-block">Drone Area</small>
                                                <strong class="fs-5">{{ number_format($building['drone_area'], 2) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-item p-2 bg-light rounded">
                                                <small class="text-muted d-block">MIS Total Area</small>
                                                <strong class="fs-5">{{ number_format($building['mis_total_area'], 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-{{ $areaVariationClass == 'danger' ? 'danger' : ($areaVariationClass == 'warning' ? 'warning' : 'success') }} mt-2 mb-0 py-2 text-center">
                                        <i class="fas fa-{{ $areaVariationIcon }} me-1"></i>
                                        Difference: {{ number_format($building['area_difference'], 2) }} sqft
                                    </div>
                                </div>

                                <!-- Counts Section -->
                                <div class="info-section mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-chart-simple me-2" style="color:#FFC107;"></i>
                                        Survey Statistics
                                    </h6>
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Points</small>
                                                <strong class="fs-5">{{ number_format($building['surveyed_points']) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Assessments</small>
                                                <strong class="fs-5">{{ number_format($building['assessment_count']) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Shops</small>
                                                <strong class="fs-5">{{ number_format($building['shop_count']) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Usage Variation Warning -->
                                @if($building['usage_variation'])
                                    <div class="alert alert-warning mb-0 py-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Usage Variation Detected!</strong>
                                        <small class="d-block mt-1">
                                            {{ count($building['usage_mismatches']) }} assessment(s) have usage mismatch
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-success mb-0 py-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        All assessments match MIS usage
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer -->
                            <div class="building-card-footer p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-sm btn-outline-primary view-details" data-gisid="{{ $building['gisid'] }}">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </button>
                                    <small class="text-muted">
                                        <i class="fas fa-hard-drive"></i> {{ count($building['assessments']) }} records
                                    </small>
                                </div>
                            </div>

                            <!-- Hidden Details Panel (initially hidden) -->
                            <div id="details-{{ $building['gisid'] }}" class="details-panel" style="display: none;">
                                <div class="p-3 border-top">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-list me-2"></i>Assessment Details
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Assessment</th>
                                                    <th>Survey Usage</th>
                                                    <th>MIS Usage</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($building['assessments'] as $assessment)
                                                    <tr class="{{ isset($assessment->mis_usage) && strtolower(trim($assessment->bill_usage ?? '')) != strtolower(trim($assessment->mis_usage)) ? 'table-warning' : '' }}">
                                                        <td>{{ $assessment->assessment }}</td>
                                                        <td>{{ $assessment->bill_usage ?? 'N/A' }}</td>
                                                        <td>{{ $assessment->mis_usage ?? 'N/A' }}</td>
                                                        <td>
                                                            @if(isset($assessment->mis_usage) && strtolower(trim($assessment->bill_usage ?? '')) != strtolower(trim($assessment->mis_usage)))
                                                                <span class="badge bg-warning">Mismatch</span>
                                                            @else
                                                                <span class="badge bg-success">Match</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($building['shops']->count() > 0)
                                        <h6 class="fw-bold mt-3 mb-2">
                                            <i class="fas fa-store me-2"></i>Shop Details
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Shop Name</th>
                                                        <th>Owner Name</th>
                                                        <th>Mobile</th>
                                                        <th>Usage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($building['shops'] as $shop)
                                                        <tr>
                                                            <td>{{ $shop->shop_name ?? 'N/A' }}</td>
                                                            <td>{{ $shop->owner_name ?? 'N/A' }}</td>
                                                            <td>{{ $shop->mobile ?? 'N/A' }}</td>
                                                            <td>{{ $shop->usage_type ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <h5>No Building Data Available</h5>
                            <p class="mb-0">No variations data found for this ward.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Table View Container (hidden by default) -->
        <div id="tableViewContainer" class="stat-card p-3" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="variationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>GIS ID</th>
                            <th>Building Name</th>
                            <th>Road Name</th>
                            <th>Drone Area</th>
                            <th>MIS Area</th>
                            <th>Difference</th>
                            <th>Variation</th>
                            <th>Usage Match</th>
                            <th>Assessments</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $building)
                            <tr>
                                <td><code>{{ $building['gisid'] }}</code></td>
                                <td>{{ $building['building_name'] ?: '-' }}</td>
                                <td>{{ $building['road_name'] ?: '-' }}</td>
                                <td>{{ number_format($building['drone_area'], 2) }}</td>
                                <td>{{ number_format($building['mis_total_area'], 2) }}</td>
                                <td class="{{ $building['area_difference'] > 0 ? 'text-danger' : ($building['area_difference'] < 0 ? 'text-warning' : 'text-success') }}">
                                    {{ number_format($building['area_difference'], 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $building['area_variation'] == 'EXCESS' ? 'danger' : ($building['area_variation'] == 'SHORT' ? 'warning' : 'success') }}">
                                        {{ $building['area_variation'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($building['usage_variation'])
                                        <span class="badge bg-warning">Mismatch</span>
                                    @else
                                        <span class="badge bg-success">Match</span>
                                    @endif
                                </td>
                                <td>{{ $building['assessment_count'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary view-details-table" data-gisid="{{ $building['gisid'] }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Store the results data for filtering
        const buildings = @json($results);
        let currentView = 'card';

        // Toggle between card and table view
        $('.view-toggle').on('click', function() {
            const view = $(this).data('view');
            currentView = view;

            $('.view-toggle').removeClass('active');
            $(this).addClass('active');

            if (view === 'card') {
                $('#cardViewContainer').show();
                $('#tableViewContainer').hide();
            } else {
                $('#cardViewContainer').hide();
                $('#tableViewContainer').show();
            }
        });

        // View Details functionality for card view
        $('.view-details').on('click', function() {
            const gisid = $(this).data('gisid');
            const detailsPanel = $('#details-' + gisid);

            // Toggle visibility
            if (detailsPanel.is(':visible')) {
                detailsPanel.slideUp();
                $(this).html('<i class="fas fa-eye me-1"></i> View Details');
            } else {
                // Close any open panels
                $('.details-panel').slideUp();
                $('.view-details').html('<i class="fas fa-eye me-1"></i> View Details');

                detailsPanel.slideDown();
                $(this).html('<i class="fas fa-eye-slash me-1"></i> Hide Details');
            }
        });

        // View Details for table view
        $(document).on('click', '.view-details-table', function() {
            const gisid = $(this).data('gisid');
            // For table view, we'll show an alert with basic info or redirect to detail page
            const building = buildings.find(b => b.gisid == gisid);
            if (building) {
                let message = `GIS ID: ${building.gisid}\n`;
                message += `Building: ${building.building_name || 'N/A'}\n`;
                message += `Area Variation: ${building.area_variation}\n`;
                message += `Difference: ${building.area_difference.toFixed(2)} sqft\n`;
                message += `Usage Variation: ${building.usage_variation ? 'Yes' : 'No'}\n`;
                message += `Assessments: ${building.assessment_count}\n`;
                message += `Shops: ${building.shop_count}`;
                alert(message);
            }
        });

        // Filtering Function
        function applyFilters() {
            const filterGisid = $('#filterGisid').val().toLowerCase().trim();
            const filterBuildingName = $('#filterBuildingName').val().toLowerCase().trim();
            const filterAreaVariation = $('#filterAreaVariation').val();
            const filterUsageVariation = $('#filterUsageVariation').val();
            const filterMinDifference = parseFloat($('#filterMinDifference').val());
            const filterMaxDifference = parseFloat($('#filterMaxDifference').val());

            let visibleCount = 0;

            $('.building-card-item').each(function() {
                const $card = $(this);
                let show = true;

                // GIS ID filter
                if (filterGisid !== '') {
                    const gisid = $card.data('gisid').toString().toLowerCase();
                    if (!gisid.includes(filterGisid)) show = false;
                }

                // Building Name filter
                if (show && filterBuildingName !== '') {
                    const buildingName = $card.data('building-name');
                    if (!buildingName || !buildingName.includes(filterBuildingName)) show = false;
                }

                // Area Variation filter
                if (show && filterAreaVariation !== '') {
                    const areaVariation = $card.data('area-variation');
                    if (areaVariation !== filterAreaVariation) show = false;
                }

                // Usage Variation filter
                if (show && filterUsageVariation !== '') {
                    const usageVariation = $card.data('usage-variation');
                    if (usageVariation !== filterUsageVariation) show = false;
                }

                // Min Difference filter
                if (show && !isNaN(filterMinDifference)) {
                    const areaDiff = Math.abs(parseFloat($card.data('area-difference')));
                    if (areaDiff < filterMinDifference) show = false;
                }

                // Max Difference filter
                if (show && !isNaN(filterMaxDifference)) {
                    const areaDiff = Math.abs(parseFloat($card.data('area-difference')));
                    if (areaDiff > filterMaxDifference) show = false;
                }

                if (show) {
                    $card.show();
                    visibleCount++;
                } else {
                    $card.hide();
                }
            });

            // Also filter table view if visible
            if (currentView === 'table') {
                filterTableView();
            }

            $('#resultCount').text(visibleCount);
        }

        function filterTableView() {
            const filterGisid = $('#filterGisid').val().toLowerCase().trim();
            const filterBuildingName = $('#filterBuildingName').val().toLowerCase().trim();
            const filterAreaVariation = $('#filterAreaVariation').val();
            const filterUsageVariation = $('#filterUsageVariation').val();
            const filterMinDifference = parseFloat($('#filterMinDifference').val());
            const filterMaxDifference = parseFloat($('#filterMaxDifference').val());

            $('#variationsTable tbody tr').each(function() {
                const $row = $(this);
                let show = true;

                // GIS ID filter
                if (filterGisid !== '') {
                    const gisid = $row.find('td:first').text().toLowerCase().trim();
                    if (!gisid.includes(filterGisid)) show = false;
                }

                // Building Name filter
                if (show && filterBuildingName !== '') {
                    const buildingName = $row.find('td:eq(1)').text().toLowerCase().trim();
                    if (!buildingName.includes(filterBuildingName)) show = false;
                }

                // Area Variation filter
                if (show && filterAreaVariation !== '') {
                    const variationBadge = $row.find('td:eq(6) .badge').text().trim();
                    if (variationBadge !== filterAreaVariation) show = false;
                }

                // Usage Variation filter
                if (show && filterUsageVariation !== '') {
                    const usageBadge = $row.find('td:eq(7) .badge').text().trim();
                    const hasMismatch = usageBadge === 'Mismatch';
                    const filterValue = filterUsageVariation === 'true';
                    if (hasMismatch !== filterValue) show = false;
                }

                // Min/Max Difference filters
                if (show && (!isNaN(filterMinDifference) || !isNaN(filterMaxDifference))) {
                    const diffText = $row.find('td:eq(5)').text().trim();
                    const diff = Math.abs(parseFloat(diffText));

                    if (!isNaN(filterMinDifference) && diff < filterMinDifference) show = false;
                    if (!isNaN(filterMaxDifference) && diff > filterMaxDifference) show = false;
                }

                $row.toggle(show);
            });
        }

        // Apply filters button click
        $('#applyFiltersBtn').on('click', function() {
            applyFilters();
        });

        // Clear filters button click
        $('#clearFiltersBtn').on('click', function() {
            $('#filterGisid').val('');
            $('#filterBuildingName').val('');
            $('#filterAreaVariation').val('');
            $('#filterUsageVariation').val('');
            $('#filterMinDifference').val('');
            $('#filterMaxDifference').val('');
            applyFilters();
        });

        // Enter key support for filter inputs
        $('.form-control, .form-select').on('keypress', function(e) {
            if (e.which === 13) {
                applyFilters();
            }
        });
    });
</script>

<style>
    .dashboard-content-area {
        padding: 20px;
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        min-height: 100vh;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.96);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        background: rgba(22, 121, 171, 0.1);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #1679AB;
    }

    /* Form Controls */
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #1679AB;
        box-shadow: 0 0 0 0.2rem rgba(22, 121, 171, 0.25);
    }

    /* Building Card Styles */
    .building-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .building-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .building-card-header {
        color: white;
    }

    .building-card-body {
        background: white;
    }

    .building-card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-section {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .info-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .info-item {
        transition: all 0.2s ease;
    }

    .info-item:hover {
        transform: scale(1.02);
    }

    /* Badge Styles */
    .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 20px;
    }

    /* Button Styles */
    .btn-sm {
        border-radius: 8px;
        padding: 5px 12px;
    }

    /* Card Entry Animation */
    @keyframes cardFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .building-card-item {
        animation: cardFadeIn 0.5s ease forwards;
    }

    /* Details Panel */
    .details-panel {
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    /* Breadcrumb */
    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }

    /* Table Styles */
    #variationsTable th {
        background-color: #102C57;
        color: white;
    }

    #variationsTable td {
        vertical-align: middle;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-content-area {
            padding: 15px;
        }

        .stat-card {
            margin-bottom: 15px;
        }

        .building-card {
            margin-bottom: 15px;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 11px;
        }

        .badge {
            font-size: 10px;
        }
    }

    @media (max-width: 576px) {
        .building-card-header {
            flex-direction: column;
            gap: 10px;
        }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #1679AB;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #102C57;
    }

    /* Progress Bar Colors */
    .progress-bar.bg-info {
        background-color: #1679AB !important;
    }

    /* Text Colors */
    .text-success { color: #28a745 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-primary { color: #1679AB !important; }

    /* Label styling */
    label {
        font-size: 0.9rem;
    }

    /* View toggle buttons */
    .view-toggle.active {
        background-color: #1679AB !important;
        border-color: #1679AB !important;
        color: white !important;
    }
</style>
@endpush
