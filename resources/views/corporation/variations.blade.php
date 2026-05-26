{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Building Variations - Ward ' . ($warddetail->ward_no ?? ''))

@section('content')
<div class="dashboard-content-area">
    <div class="animate__animated animate__fadeInUp">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold text-white">
                <i class="fas fa-chart-line me-2" style="color:#1679AB;"></i>
                Building Variations - Ward {{ $warddetail->ward_no ?? '' }}
            </h3>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
                <a href="{{ route('corporation.dashboard') }}" class="btn btn-sm btn-light ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Ward Info Card -->
        <div class="stat-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color:#102C57;">Ward {{ $warddetail->ward_no ?? '' }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-building me-1"></i> Zone: {{ ucfirst($warddetail->zone ?? 'N/A') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-primary p-2">
                        <i class="fas fa-chart-line me-1"></i> Variation Analysis Report
                    </span>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button onclick="exportToExcel()" class="btn btn-sm btn-success ms-2">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalBuildings) }}</h2>
                        <small class="text-info"><i class="fas fa-building"></i> In this ward</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Sq. Feet</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalSqfeet, 2) }}</h2>
                        <small class="text-info"><i class="fas fa-vector-square"></i> Built-up area</small>
                    </div>
                    <div class="stat-icon bg-info-subtle"><i class="fas fa-ruler-combined text-info"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Plot Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalMisPlotArea, 2) }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> From MIS records</small>
                    </div>
                    <div class="stat-icon bg-warning-subtle"><i class="fas fa-database text-warning"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Calculated Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalCalculatedArea, 2) }}</h2>
                        <small class="text-danger"><i class="fas fa-calculator"></i> Based on formula</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-calculator text-danger"></i></div>
                </div>
            </div>
        </div>

        <!-- Variation Summary Card -->
        @php
            $variationColor = $totalAreaVariation >= 0 ? 'success' : 'danger';
            $variationIcon = $totalAreaVariation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            $variationText = $totalAreaVariation >= 0 ? 'Under-assessment' : 'Over-assessment';
        @endphp

        <div class="stat-card p-4 mb-4" style="background: linear-gradient(135deg, {{ $totalAreaVariation >= 0 ? '#d4edda' : '#f8d7da' }} 0%, #ffffff 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3" style="background: {{ $totalAreaVariation >= 0 ? 'rgba(40, 167, 69, 0.2)' : 'rgba(220, 53, 69, 0.2)' }}">
                            <i class="fas {{ $variationIcon }} {{ $totalAreaVariation >= 0 ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color:#102C57;">
                                Total Area Variation:
                                <span class="{{ $totalAreaVariation >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}
                                </span>
                            </h5>
                            <p class="mb-0">
                                <span class="badge {{ $totalAreaVariation >= 0 ? 'bg-success' : 'bg-danger' }} p-2">
                                    <i class="fas {{ $variationIcon }} me-1"></i>
                                    {{ $variationText }} ({{ $avgVariationPercentage >= 0 ? '+' : '' }}{{ number_format($avgVariationPercentage, 2) }}% Average)
                                </span>
                                <small class="text-muted ms-2">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $totalAreaVariation >= 0 ? 'Positive variation indicates potential revenue loss' : 'Negative variation indicates possible over-assessment' }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="progress" style="height: 10px;">
                        @php
                            $progressPercent = min(100, abs($avgVariationPercentage));
                        @endphp
                        <div class="progress-bar {{ $totalAreaVariation >= 0 ? 'bg-success' : 'bg-danger' }}"
                             style="width: {{ $progressPercent }}%">
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        {{ $progressPercent }}% deviation from MIS records
                    </small>
                </div>
            </div>
        </div>

        <!-- Advanced Filters Section -->
        <div class="stat-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-filter me-2" style="color:#1679AB;"></i>
                    Advanced Filters
                </h5>
                <button class="btn btn-sm btn-outline-secondary" id="clearFiltersBtn">
                    <i class="fas fa-undo-alt me-1"></i> Clear All Filters
                </button>
            </div>

            <div class="row g-3">
                <!-- GIS ID Search -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">GIS ID</label>
                    <input type="text" id="gisidSearch" class="form-control" placeholder="Enter GIS ID...">
                </div>

                <!-- Floors Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Floors</label>
                    <select id="floorsFilter" class="form-select">
                        <option value="">All</option>
                        <option value="1">1 Floor</option>
                        <option value="2">2 Floors</option>
                        <option value="3">3 Floors</option>
                        <option value="4">4+ Floors</option>
                    </select>
                </div>

                <!-- Variation Type Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Variation Type</label>
                    <select id="variationTypeFilter" class="form-select">
                        <option value="">All</option>
                        <option value="positive">Under-assessment (Positive)</option>
                        <option value="negative">Over-assessment (Negative)</option>
                        <option value="zero">No Variation (Zero)</option>
                    </select>
                </div>

                <!-- Variation Range -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Variation % Range</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="variationMin" class="form-control" placeholder="Min %" step="any">
                        <span class="align-self-center">to</span>
                        <input type="number" id="variationMax" class="form-control" placeholder="Max %" step="any">
                    </div>
                </div>

                <!-- Basement Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Basement</label>
                    <select id="basementFilter" class="form-select">
                        <option value="">All</option>
                        <option value="0">No Basement</option>
                        <option value="1">Has Basement</option>
                    </select>
                </div>

                <!-- Floor Percentage Range -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Floor % Range</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="percentageMin" class="form-control" placeholder="Min %" step="any">
                        <span class="align-self-center">to</span>
                        <input type="number" id="percentageMax" class="form-control" placeholder="Max %" step="any">
                    </div>
                </div>

                <!-- Sq Feet Range -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Sq. Feet Range</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="sqfeetMin" class="form-control" placeholder="Min" step="any">
                        <span class="align-self-center">to</span>
                        <input type="number" id="sqfeetMax" class="form-control" placeholder="Max" step="any">
                    </div>
                </div>

                <!-- Area Variation Range -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Area Variation Range</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="areaVariationMin" class="form-control" placeholder="Min" step="any">
                        <span class="align-self-center">to</span>
                        <input type="number" id="areaVariationMax" class="form-control" placeholder="Max" step="any">
                    </div>
                </div>

                <!-- Assessment Count Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Assessment Points</label>
                    <select id="assessmentCountFilter" class="form-select">
                        <option value="">All</option>
                        <option value="1">Single Assessment</option>
                        <option value="2">Multiple Assessments (2+)</option>
                    </select>
                </div>
            </div>

            <!-- Active Filters Display -->
            <div id="activeFilters" class="mt-3 pt-2 border-top" style="display: none;">
                <small class="text-muted me-2">Active Filters:</small>
                <div id="filterTags" class="d-flex flex-wrap gap-2 mt-1"></div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                    Detailed Building Data
                    <span class="badge bg-primary ms-2" id="recordCount">0 Records</span>
                </h4>
                <div class="mt-2 mt-sm-0">
                    <select id="perPageSelect" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                        <option value="25">25 per page</option>
                        <option value="50" selected>50 per page</option>
                        <option value="100">100 per page</option>
                        <option value="200">200 per page</option>
                        <option value="500">500 per page</option>
                        <option value="-1">All Records</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="variationsTable">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="index">S.No <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable" data-column="gisid">GIS ID <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-end" data-column="sqfeet">Sq. Feet <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-center" data-column="number_floor">Floors <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-center" data-column="percentage">Floor % <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-center" data-column="basement">Basement <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-end" data-column="mis_plot_area">MIS Area <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-end" data-column="calculated_area">Calculated <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-end" data-column="area_variation">Variation <i class="fas fa-sort ms-1"></i></th>
                            <th class="sortable text-center" data-column="variation_percentage">Variation % <i class="fas fa-sort ms-1"></i></th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Map View</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Dynamic content will be loaded here -->
                    </tbody>
                    <tfoot id="tableFooter" style="background: #f8f9fa; font-weight: bold;">
                        <!-- Dynamic footer will be loaded here -->
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                <!-- Dynamic pagination will be loaded here -->
            </div>
        </div>

        <!-- Info Note with Corrected Formula -->
        <div class="stat-card p-3 mt-4" style="background: #e7f3ff;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-lightbulb fa-2x" style="color:#1679AB;"></i>
                </div>
                <div>
                    <strong class="d-block">Understanding the Calculations</strong>
                    <small class="text-muted">
                        <strong>Formula:</strong> Calculated Area = (Number of Floors + Basement + (Floor % / 100)) × Sq. Feet<br>
                        <strong>Example:</strong> If Floors=2, Basement=1, Floor%=80, Sq.Feet=1000<br>
                        = (2 + 1 + 0.8) × 1000 = 3.8 × 1000 = 3800 sq.ft<br>
                        <strong>Area Variation</strong> = Calculated Area - MIS Plot Area
                        (<span class="text-success">Positive = Under-assessment</span>,
                        <span class="text-danger">Negative = Over-assessment</span>)<br>
                        <strong>Note:</strong> All totals reflect ALL buildings in this ward, not just the current page.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Google Maps Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #102C57 0%, #1679AB 100%); color: white;">
                <h5 class="modal-title" id="mapModalLabel">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    Location Map
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="simpleMap" style="height: 400px; width: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <i class="fas fa-map fa-3x text-muted mb-2"></i>
                        <p>Click "Open in Google Maps" to view location</p>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <a href="#" id="openGoogleMaps" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Open in Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // Store ALL data from PHP
    let allData = @json($allDataJson);

    // Parse if it's a string
    if (typeof allData === 'string') {
        allData = JSON.parse(allData);
    }

    let filteredData = [...allData];
    let currentPage = 1;
    let itemsPerPage = 50;
    let currentSort = { column: 'index', direction: 'asc' };

    // Totals from server (ward totals - never change)
    const serverTotals = {
        totalBuildings: {{ $totalBuildings }},
        totalSqfeet: {{ $totalSqfeet }},
        totalMisPlotArea: {{ $totalMisPlotArea }},
        totalCalculatedArea: {{ $totalCalculatedArea }},
        totalAreaVariation: {{ $totalAreaVariation }},
        avgVariationPercentage: {{ $avgVariationPercentage }}
    };

    $(document).ready(function() {
        console.log('Total records loaded:', allData.length);
        initializeFilters();
        applyFiltersAndRender();
    });

    function initializeFilters() {
        // Bind filter events
        $('#gisidSearch, #floorsFilter, #variationTypeFilter, #variationMin, #variationMax, #basementFilter, #percentageMin, #percentageMax, #sqfeetMin, #sqfeetMax, #areaVariationMin, #areaVariationMax, #assessmentCountFilter').on('keyup change', function() {
            currentPage = 1;
            applyFiltersAndRender();
        });

        // Clear filters button
        $('#clearFiltersBtn').click(function() {
            clearAllFilters();
            applyFiltersAndRender();
        });

        // Per page change
        $('#perPageSelect').change(function() {
            const val = parseInt($(this).val());
            itemsPerPage = val === -1 ? allData.length : val;
            currentPage = 1;
            renderTable();
        });

        // Sorting
        $('.sortable').click(function() {
            const column = $(this).data('column');
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = column;
                currentSort.direction = 'asc';
            }
            applyFiltersAndRender();
        });
    }

    function clearAllFilters() {
        $('#gisidSearch').val('');
        $('#floorsFilter').val('');
        $('#variationTypeFilter').val('');
        $('#variationMin').val('');
        $('#variationMax').val('');
        $('#basementFilter').val('');
        $('#percentageMin').val('');
        $('#percentageMax').val('');
        $('#sqfeetMin').val('');
        $('#sqfeetMax').val('');
        $('#areaVariationMin').val('');
        $('#areaVariationMax').val('');
        $('#assessmentCountFilter').val('');
        $('#activeFilters').hide();
        $('#filterTags').empty();
    }

    function applyFiltersAndRender() {
        filteredData = [...allData];

        // Apply all filters
        filteredData = filteredData.filter(item => applyGISIDFilter(item));
        filteredData = filteredData.filter(item => applyFloorsFilter(item));
        filteredData = filteredData.filter(item => applyVariationTypeFilter(item));
        filteredData = filteredData.filter(item => applyVariationRangeFilter(item));
        filteredData = filteredData.filter(item => applyBasementFilter(item));
        filteredData = filteredData.filter(item => applyPercentageRangeFilter(item));
        filteredData = filteredData.filter(item => applySqfeetRangeFilter(item));
        filteredData = filteredData.filter(item => applyAreaVariationRangeFilter(item));
        filteredData = filteredData.filter(item => applyAssessmentCountFilter(item));

        // Apply sorting
        filteredData = sortData(filteredData);

        // Update record count
        $('#recordCount').text(filteredData.length + ' Records');

        // Update active filters display
        updateActiveFiltersDisplay();

        // Reset to page 1 if current page is out of bounds
        const maxPage = Math.ceil(filteredData.length / itemsPerPage);
        if (currentPage > maxPage && maxPage > 0) {
            currentPage = maxPage;
        }

        renderTable();
    }

    function applyGISIDFilter(item) {
        const search = $('#gisidSearch').val().toLowerCase();
        if (!search) return true;
        return item.gisid.toLowerCase().includes(search);
    }

    function applyFloorsFilter(item) {
        const floors = $('#floorsFilter').val();
        if (!floors) return true;
        if (floors === '4') {
            return item.number_floor >= 4;
        }
        return item.number_floor == floors;
    }

    function applyVariationTypeFilter(item) {
        const type = $('#variationTypeFilter').val();
        if (!type) return true;
        if (type === 'positive') return item.area_variation > 0;
        if (type === 'negative') return item.area_variation < 0;
        if (type === 'zero') return Math.abs(item.area_variation) < 0.01;
        return true;
    }

    function applyVariationRangeFilter(item) {
        const min = parseFloat($('#variationMin').val());
        const max = parseFloat($('#variationMax').val());

        if (!isNaN(min) && item.variation_percentage < min) return false;
        if (!isNaN(max) && item.variation_percentage > max) return false;
        return true;
    }

    function applyBasementFilter(item) {
        const basement = $('#basementFilter').val();
        if (!basement) return true;
        if (basement === '0') return item.basement === 0;
        if (basement === '1') return item.basement > 0;
        return true;
    }

    function applyPercentageRangeFilter(item) {
        const min = parseFloat($('#percentageMin').val());
        const max = parseFloat($('#percentageMax').val());

        if (!isNaN(min) && item.percentage < min) return false;
        if (!isNaN(max) && item.percentage > max) return false;
        return true;
    }

    function applySqfeetRangeFilter(item) {
        const min = parseFloat($('#sqfeetMin').val());
        const max = parseFloat($('#sqfeetMax').val());

        if (!isNaN(min) && item.sqfeet < min) return false;
        if (!isNaN(max) && item.sqfeet > max) return false;
        return true;
    }

    function applyAreaVariationRangeFilter(item) {
        const min = parseFloat($('#areaVariationMin').val());
        const max = parseFloat($('#areaVariationMax').val());

        if (!isNaN(min) && item.area_variation < min) return false;
        if (!isNaN(max) && item.area_variation > max) return false;
        return true;
    }

    function applyAssessmentCountFilter(item) {
        const count = $('#assessmentCountFilter').val();
        if (!count) return true;
        if (count === '1') return item.assessment_count === 1;
        if (count === '2') return item.assessment_count >= 2;
        return true;
    }

    function sortData(data) {
        const sorted = [...data];
        const column = currentSort.column;
        const direction = currentSort.direction;

        sorted.sort((a, b) => {
            let aVal, bVal;

            switch(column) {
                case 'index':
                    return direction === 'asc' ? a.index - b.index : b.index - a.index;
                case 'gisid':
                    aVal = String(a.gisid).toLowerCase();
                    bVal = String(b.gisid).toLowerCase();
                    if (aVal < bVal) return direction === 'asc' ? -1 : 1;
                    if (aVal > bVal) return direction === 'asc' ? 1 : -1;
                    return 0;
                case 'sqfeet':
                    aVal = a.sqfeet;
                    bVal = b.sqfeet;
                    break;
                case 'number_floor':
                    aVal = a.number_floor;
                    bVal = b.number_floor;
                    break;
                case 'percentage':
                    aVal = a.percentage;
                    bVal = b.percentage;
                    break;
                case 'basement':
                    aVal = a.basement;
                    bVal = b.basement;
                    break;
                case 'mis_plot_area':
                    aVal = a.mis_plot_area;
                    bVal = b.mis_plot_area;
                    break;
                case 'calculated_area':
                    aVal = a.calculated_area;
                    bVal = b.calculated_area;
                    break;
                case 'area_variation':
                    aVal = a.area_variation;
                    bVal = b.area_variation;
                    break;
                case 'variation_percentage':
                    aVal = a.variation_percentage;
                    bVal = b.variation_percentage;
                    break;
                default:
                    return 0;
            }

            if (aVal < bVal) return direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return direction === 'asc' ? 1 : -1;
            return 0;
        });

        return sorted;
    }

    function updateActiveFiltersDisplay() {
        const filters = [];

        const gisid = $('#gisidSearch').val();
        if (gisid) filters.push(`GIS ID: ${gisid}`);

        const floors = $('#floorsFilter').val();
        if (floors) filters.push(`Floors: ${floors === '4' ? '4+' : floors}`);

        const variationType = $('#variationTypeFilter').val();
        if (variationType) filters.push(`Variation: ${variationType === 'positive' ? 'Under-assessment' : variationType === 'negative' ? 'Over-assessment' : 'No Variation'}`);

        const variationMin = $('#variationMin').val();
        const variationMax = $('#variationMax').val();
        if (variationMin || variationMax) filters.push(`Variation %: ${variationMin || 'Any'} to ${variationMax || 'Any'}`);

        const basement = $('#basementFilter').val();
        if (basement) filters.push(`Basement: ${basement === '0' ? 'No' : 'Yes'}`);

        const percentageMin = $('#percentageMin').val();
        const percentageMax = $('#percentageMax').val();
        if (percentageMin || percentageMax) filters.push(`Floor %: ${percentageMin || 'Any'} to ${percentageMax || 'Any'}`);

        const sqfeetMin = $('#sqfeetMin').val();
        const sqfeetMax = $('#sqfeetMax').val();
        if (sqfeetMin || sqfeetMax) filters.push(`Sq.Feet: ${sqfeetMin || 'Any'} to ${sqfeetMax || 'Any'}`);

        const areaVarMin = $('#areaVariationMin').val();
        const areaVarMax = $('#areaVariationMax').val();
        if (areaVarMin || areaVarMax) filters.push(`Area Var: ${areaVarMin || 'Any'} to ${areaVarMax || 'Any'}`);

        const assessmentCount = $('#assessmentCountFilter').val();
        if (assessmentCount) filters.push(`Assessments: ${assessmentCount === '1' ? 'Single' : 'Multiple'}`);

        if (filters.length > 0) {
            $('#activeFilters').show();
            $('#filterTags').html(filters.map(f => `<span class="badge bg-secondary">${escapeHtml(f)}</span>`).join(''));
        } else {
            $('#activeFilters').hide();
        }
    }

    function parseCoordinates(item) {
        if (!item.coordinates) return null;

        try {
            // If coordinates is a string, try to parse it
            let coords = item.coordinates;

            // Handle GeoJSON format
            if (typeof coords === 'string') {
                const parsed = JSON.parse(coords);
                if (parsed.type === 'Point' && parsed.coordinates && parsed.coordinates.length >= 2) {
                    return {
                        lat: parsed.coordinates[1],
                        lng: parsed.coordinates[0]
                    };
                }
            }

            // Handle "lat,lng" format
            if (typeof coords === 'string' && coords.includes(',')) {
                const parts = coords.split(',');
                if (parts.length >= 2) {
                    const lat = parseFloat(parts[0].trim());
                    const lng = parseFloat(parts[1].trim());
                    if (!isNaN(lat) && !isNaN(lng)) {
                        return { lat, lng };
                    }
                }
            }

            // Handle object format
            if (typeof coords === 'object') {
                if (coords.lat && coords.lng) {
                    return { lat: coords.lat, lng: coords.lng };
                }
                if (coords.latitude && coords.longitude) {
                    return { lat: coords.latitude, lng: coords.longitude };
                }
                if (coords.coordinates && coords.coordinates.length >= 2) {
                    return { lat: coords.coordinates[1], lng: coords.coordinates[0] };
                }
            }
        } catch(e) {
            console.log('Error parsing coordinates:', e);
        }

        return null;
    }

    function openGoogleMaps(gisid, coordinates) {
        const coords = parseCoordinates({ coordinates });

        if (coords && coords.lat && coords.lng) {
            const url = `https://www.google.com/maps?q=${coords.lat},${coords.lng}`;
            window.open(url, '_blank');
        } else {
            // If no coordinates, search by GIS ID
            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(gisid)}`;
            window.open(url, '_blank');
        }
    }

    function renderTable() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = filteredData.slice(start, end);

        // Calculate filtered totals
        const filteredTotals = calculateFilteredTotals();

        // Render table body
        let html = '';
        pageData.forEach((item, idx) => {
            const variationClass = item.area_variation >= 0 ? 'text-success' : 'text-danger';
            const variationIcon = item.area_variation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            const serialNo = start + idx + 1;

            // Check if coordinates exist
            const hasCoordinates = item.coordinates && (
                (typeof item.coordinates === 'string' && item.coordinates.length > 0) ||
                (typeof item.coordinates === 'object' && Object.keys(item.coordinates).length > 0)
            );

            html += `
                <tr>
                    <td>${serialNo}</td>
                    <td>
                        <span class="badge bg-info bg-opacity-10 text-dark p-2" style="cursor: pointer;" onclick="openGoogleMaps('${escapeHtml(item.gisid)}', ${JSON.stringify(item.coordinates)})">
                            <i class="fas fa-map-pin me-1"></i>
                            ${escapeHtml(item.gisid)}
                        </span>
                    </td>
                    <td class="text-end fw-semibold">${formatNumber(item.sqfeet, 2)}</td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            ${item.number_floor}
                        </span>
                    </td>
                    <td class="text-center">${formatNumber(item.percentage, 1)}%</td>
                    <td class="text-center">
                        ${item.basement > 0 ?
                            `<span class="badge bg-warning bg-opacity-10 text-warning">${item.basement}</span>` :
                            '<span class="text-muted">—</span>'}
                    </td>
                    <td class="text-end">${formatNumber(item.mis_plot_area, 2)}</td>
                    <td class="text-end">${formatNumber(item.calculated_area, 2)}</td>
                    <td class="text-end ${variationClass}">
                        <i class="fas ${variationIcon} me-1"></i>
                        ${item.area_variation >= 0 ? '+' : ''}${formatNumber(item.area_variation, 2)}
                    </td>
                    <td class="text-center ${variationClass}">
                        ${item.variation_percentage >= 0 ? '+' : ''}${formatNumber(item.variation_percentage, 2)}%
                    </td>
                    <td class="text-center">
                        <span class="badge ${item.assessment_count > 1 ? 'bg-info' : 'bg-secondary'}">
                            ${item.assessment_count}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm ${hasCoordinates ? 'btn-primary' : 'btn-secondary'}"
                                onclick="openGoogleMaps('${escapeHtml(item.gisid)}', ${JSON.stringify(item.coordinates)})"
                                ${!hasCoordinates ? 'disabled' : ''}
                                title="${hasCoordinates ? 'View on Google Maps' : 'No coordinates available'}">
                            <i class="fas fa-map-marked-alt"></i>
                            ${hasCoordinates ? 'View Map' : 'No Map'}
                        </button>
                    </td>
                </tr>
            `;
        });

        if (pageData.length === 0) {
            html = `
                <tr>
                    <td colspan="12" class="text-center py-5">
                        <i class="fas fa-filter fa-3x text-muted mb-3 d-block"></i>
                        <h5>No Matching Records</h5>
                        <p class="text-muted mb-0">Try adjusting your filters to see more results</p>
                    </td>
                </tr>
            `;
        }

        $('#tableBody').html(html);

        // Render footer with filtered totals
        const footerHtml = `
            <tr>
                <td colspan="2"><strong>FILTERED TOTAL</strong></td>
                <td class="text-end"><strong>${formatNumber(filteredTotals.totalSqfeet, 2)}</strong></td>
                <td colspan="3"></td>
                <td class="text-end"><strong>${formatNumber(filteredTotals.totalMisPlotArea, 2)}</strong></td>
                <td class="text-end"><strong>${formatNumber(filteredTotals.totalCalculatedArea, 2)}</strong></td>
                <td class="text-end ${filteredTotals.totalAreaVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredTotals.totalAreaVariation >= 0 ? '+' : ''}${formatNumber(filteredTotals.totalAreaVariation, 2)}</strong>
                </td>
                <td class="text-center ${filteredTotals.avgVariationPercentage >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredTotals.avgVariationPercentage >= 0 ? '+' : ''}${formatNumber(filteredTotals.avgVariationPercentage, 2)}%</strong>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr style="border-top: 2px solid #dee2e6;">
                <td colspan="2"><strong>WARD TOTAL</strong></td>
                <td class="text-end"><strong>${formatNumber(serverTotals.totalSqfeet, 2)}</strong></td>
                <td colspan="3"></td>
                <td class="text-end"><strong>${formatNumber(serverTotals.totalMisPlotArea, 2)}</strong></td>
                <td class="text-end"><strong>${formatNumber(serverTotals.totalCalculatedArea, 2)}</strong></td>
                <td class="text-end ${serverTotals.totalAreaVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.totalAreaVariation >= 0 ? '+' : ''}${formatNumber(serverTotals.totalAreaVariation, 2)}</strong>
                </td>
                <td class="text-center ${serverTotals.avgVariationPercentage >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.avgVariationPercentage >= 0 ? '+' : ''}${formatNumber(serverTotals.avgVariationPercentage, 2)}%</strong>
                </td>
                <td colspan="2"></td>
            </tr>
        `;
        $('#tableFooter').html(footerHtml);

        // Render pagination
        renderPagination();
    }

    function calculateFilteredTotals() {
        let totalSqfeet = 0;
        let totalMisPlotArea = 0;
        let totalCalculatedArea = 0;
        let totalAreaVariation = 0;

        filteredData.forEach(item => {
            totalSqfeet += item.sqfeet;
            totalMisPlotArea += item.mis_plot_area;
            totalCalculatedArea += item.calculated_area;
            totalAreaVariation += item.area_variation;
        });

        const avgVariationPercentage = totalMisPlotArea > 0 ? (totalAreaVariation / totalMisPlotArea) * 100 : 0;

        return {
            totalSqfeet,
            totalMisPlotArea,
            totalCalculatedArea,
            totalAreaVariation,
            avgVariationPercentage
        };
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredData.length);

        if (totalPages <= 1 && filteredData.length <= itemsPerPage) {
            $('#pagination').html(`
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing ${filteredData.length > 0 ? start : 0} to ${end} of ${filteredData.length} records
                    </small>
                </div>
                <div></div>
            `);
            return;
        }

        let paginationHtml = `
            <div class="mb-2 mb-sm-0">
                <small class="text-muted">
                    Showing ${filteredData.length > 0 ? start : 0} to ${end} of ${filteredData.length} records
                </small>
            </div>
            <div>
                <nav>
                    <ul class="pagination mb-0">
        `;

        // Previous button
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
            </li>
        `;

        // Page numbers
        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1); return false;">1</a></li>`;
            if (startPage > 2) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a></li>`;
        }

        // Next button
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next</a>
            </li>
        `;

        paginationHtml += `
                    </ul>
                </nav>
            </div>
        `;

        $('#pagination').html(paginationHtml);
    }

    function changePage(page) {
        if (page < 1 || page > Math.ceil(filteredData.length / itemsPerPage)) return;
        currentPage = page;
        renderTable();
    }

    function formatNumber(num, decimals = 2) {
        if (isNaN(num)) return '0.00';
        return parseFloat(num).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function exportToExcel() {
        const exportData = filteredData.map(item => ({
            'S.No': item.index,
            'GIS ID': item.gisid,
            'Sq. Feet': item.sqfeet,
            'Floors': item.number_floor,
            'Floor %': item.percentage,
            'Basement': item.basement,
            'MIS Plot Area': item.mis_plot_area,
            'Calculated Area': item.calculated_area,
            'Area Variation': item.area_variation,
            'Variation %': item.variation_percentage,
            'Assessment Count': item.assessment_count,
            'Coordinates': typeof item.coordinates === 'object' ? JSON.stringify(item.coordinates) : (item.coordinates || '')
        }));

        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();

        // Add summary info
        const summaryData = [
            ['BUILDING VARIATION REPORT'],
            ['Generated on:', new Date().toLocaleString()],
            ['Ward No:', '{{ $warddetail->ward_no ?? '' }}'],
            ['Zone:', '{{ ucfirst($warddetail->zone ?? 'N/A') }}'],
            [''],
            ['SUMMARY STATISTICS (Filtered Data)'],
            ['Total Buildings:', filteredData.length],
            ['Total Sq. Feet:', formatNumber(calculateFilteredTotals().totalSqfeet, 2)],
            ['Total MIS Plot Area:', formatNumber(calculateFilteredTotals().totalMisPlotArea, 2)],
            ['Total Calculated Area:', formatNumber(calculateFilteredTotals().totalCalculatedArea, 2)],
            ['Total Area Variation:', `${calculateFilteredTotals().totalAreaVariation >= 0 ? '+' : ''}${formatNumber(calculateFilteredTotals().totalAreaVariation, 2)}`],
            ['Average Variation %:', `${calculateFilteredTotals().avgVariationPercentage >= 0 ? '+' : ''}${formatNumber(calculateFilteredTotals().avgVariationPercentage, 2)}%`],
            [''],
            ['WARD TOTAL STATISTICS'],
            ['Total Buildings:', formatNumber(serverTotals.totalBuildings, 0)],
            ['Total Sq. Feet:', formatNumber(serverTotals.totalSqfeet, 2)],
            ['Total MIS Plot Area:', formatNumber(serverTotals.totalMisPlotArea, 2)],
            ['Total Calculated Area:', formatNumber(serverTotals.totalCalculatedArea, 2)],
            ['Total Area Variation:', `${serverTotals.totalAreaVariation >= 0 ? '+' : ''}${formatNumber(serverTotals.totalAreaVariation, 2)}`],
            ['Average Variation %:', `${serverTotals.avgVariationPercentage >= 0 ? '+' : ''}${formatNumber(serverTotals.avgVariationPercentage, 2)}%`],
            [''],
            ['FORMULA USED:'],
            ['Calculated Area = (Number of Floors + Basement + (Floor % / 100)) × Sq. Feet'],
            [''],
            ['DETAILED DATA']
        ];

        XLSX.utils.sheet_add_aoa(ws, summaryData, { origin: 'A1' });

        // Adjust column widths
        ws['!cols'] = [
            {wch:8}, {wch:14}, {wch:12}, {wch:8},
            {wch:10}, {wch:8}, {wch:14}, {wch:14},
            {wch:14}, {wch:12}, {wch:14}, {wch:20}
        ];

        XLSX.utils.book_append_sheet(wb, ws, 'Ward_{{ $warddetail->ward_no ?? '' }}_Variations');
        XLSX.writeFile(wb, 'Ward_{{ $warddetail->ward_no ?? '' }}_Building_Variations.xlsx');
    }

    // Print styling
    window.onbeforeprint = function() {
        document.querySelectorAll('.btn, .menu-toggle, .navbar-custom .dropdown, #activeFilters, .sortable').forEach(el => {
            if(el) el.style.display = 'none';
        });
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        if(sidebar) sidebar.style.display = 'none';
        if(mainContent) {
            mainContent.style.width = '100%';
            mainContent.style.marginLeft = '0';
        }
    };

    window.onafterprint = function() {
        location.reload();
    };
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

    /* Table Styles - Fixed Header Visibility */
    .table {
        margin-bottom: 0;
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead th {
        border-bottom: none;
        padding: 15px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        user-select: none;
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        color: #ffffff !important;
        border: none;
    }

    .table thead th i {
        color: rgba(255, 255, 255, 0.7);
    }

    .table thead th:hover {
        background: linear-gradient(135deg, #1a3d6e 0%, #1e8bc2 100%);
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        background-color: #ffffff;
    }

    .table tbody tr:hover {
        background-color: rgba(22, 121, 171, 0.05);
    }

    .table tbody tr:hover td {
        background-color: transparent;
    }

    .table tfoot td {
        padding: 12px 15px;
        background: #f8f9fa;
        border-top: 2px solid #1679AB;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #1679AB;
        box-shadow: 0 0 0 0.2rem rgba(22, 121, 171, 0.25);
    }

    .btn-sm {
        border-radius: 8px;
        padding: 5px 12px;
    }

    .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 20px;
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        color: #102C57;
        border-radius: 8px;
        margin: 0 2px;
        border: none;
        padding: 8px 14px;
    }

    .page-item.active .page-link {
        background-color: #1679AB;
        border-color: #1679AB;
        color: white;
    }

    .page-link:hover {
        color: #1679AB;
        background-color: #f8f9fa;
    }

    /* Make GIS ID clickable */
    .table tbody td:first-child .badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .table tbody td:first-child .badge:hover {
        transform: scale(1.05);
        background-color: #1679AB !important;
        color: white !important;
    }

    .table tbody td:first-child .badge:hover i {
        color: white !important;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate__fadeInUp {
        animation-name: fadeInUp;
        animation-duration: 0.6s;
    }

    @media (max-width: 768px) {
        .dashboard-content-area {
            padding: 15px;
        }

        .stat-card {
            margin-bottom: 15px;
        }

        .table {
            font-size: 0.8rem;
        }

        .table thead th,
        .table tbody td {
            padding: 8px 10px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }

        .stat-card h2 {
            font-size: 1.3rem;
        }

        .stat-card h6 {
            font-size: 0.7rem;
        }
    }
</style>
@endpush
