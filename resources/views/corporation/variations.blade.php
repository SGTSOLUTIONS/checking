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

        <!-- Statistics Cards -->
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
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
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
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">GIS ID</label>
                    <input type="text" id="gisidSearch" class="form-control" placeholder="Enter GIS ID...">
                </div>
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
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Variation Type</label>
                    <select id="variationTypeFilter" class="form-select">
                        <option value="">All</option>
                        <option value="positive">Under-assessment</option>
                        <option value="negative">Over-assessment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Variation % Range</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="variationMin" class="form-control" placeholder="Min %">
                        <span>to</span>
                        <input type="number" id="variationMax" class="form-control" placeholder="Max %">
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                    Detailed Building Data
                    <span class="badge bg-primary ms-2" id="recordCount">0 Records</span>
                </h4>
                <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;">
                    <option value="25">25 per page</option>
                    <option value="50" selected>50 per page</option>
                    <option value="100">100 per page</option>
                    <option value="-1">All Records</option>
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="variationsTable">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="index">S.No</th>
                            <th class="sortable" data-column="gisid">GIS ID</th>
                            <th class="sortable text-end" data-column="sqfeet">Sq. Feet</th>
                            <th class="sortable text-center" data-column="number_floor">Floors</th>
                            <th class="sortable text-center" data-column="percentage">Floor %</th>
                            <th class="sortable text-center" data-column="basement">Basement</th>
                            <th class="sortable text-end" data-column="mis_plot_area">MIS Area</th>
                            <th class="sortable text-end" data-column="calculated_area">Calculated</th>
                            <th class="sortable text-end" data-column="area_variation">Variation</th>
                            <th class="sortable text-center" data-column="variation_percentage">Variation %</th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Map View</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="12" class="text-center py-5">Loading...</td></tr>
                    </tbody>
                    <tfoot id="tableFooter"></tfoot>
                </table>
            </div>

            <div id="pagination" class="d-flex justify-content-between align-items-center mt-4"></div>
        </div>

        <!-- Info Note -->
        <div class="stat-card p-3 mt-4" style="background: #e7f3ff;">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3" style="color:#1679AB;"></i>
                <div>
                    <strong>Note:</strong> GIS ID is clickable - it will search the location on Google Maps using the GIS ID.<br>
                    <small class="text-muted">Coordinates are in projected format. Clicking the map button will search by GIS ID.</small>
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

    // Parse JSON string if needed
    if (typeof allData === 'string') {
        allData = JSON.parse(allData);
    }

    let filteredData = [...allData];
    let currentPage = 1;
    let itemsPerPage = 50;
    let currentSort = { column: 'index', direction: 'asc' };

    const serverTotals = {
        totalBuildings: {{ $totalBuildings }},
        totalSqfeet: {{ $totalSqfeet }},
        totalMisPlotArea: {{ $totalMisPlotArea }},
        totalCalculatedArea: {{ $totalCalculatedArea }},
        totalAreaVariation: {{ $totalAreaVariation }},
        avgVariationPercentage: {{ $avgVariationPercentage }}
    };

    $(document).ready(function () {

        console.log('Loaded Records:', allData.length);

        initializeFilters();
        applyFiltersAndRender();

    });

    /*
    |--------------------------------------------------------------------------
    | FILTERS
    |--------------------------------------------------------------------------
    */
    function initializeFilters() {

        $('#gisidSearch, #floorsFilter, #variationTypeFilter, #variationMin, #variationMax')
            .on('keyup change', function () {

                currentPage = 1;
                applyFiltersAndRender();

            });

        $('#clearFiltersBtn').click(function () {

            $('#gisidSearch').val('');
            $('#floorsFilter').val('');
            $('#variationTypeFilter').val('');
            $('#variationMin').val('');
            $('#variationMax').val('');

            currentPage = 1;

            applyFiltersAndRender();

        });

        $('#perPageSelect').change(function () {

            let value = parseInt($(this).val());

            itemsPerPage = value === -1 ? filteredData.length : value;

            currentPage = 1;

            renderTable();

        });

        $('.sortable').click(function () {

            const column = $(this).data('column');

            if (currentSort.column === column) {
                currentSort.direction =
                    currentSort.direction === 'asc'
                        ? 'desc'
                        : 'asc';
            } else {
                currentSort.column = column;
                currentSort.direction = 'asc';
            }

            applyFiltersAndRender();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | APPLY FILTERS
    |--------------------------------------------------------------------------
    */
    function applyFiltersAndRender() {

        filteredData = [...allData];

        // GIS ID SEARCH
        const gisidSearch = $('#gisidSearch').val().toLowerCase();

        if (gisidSearch) {

            filteredData = filteredData.filter(item =>
                String(item.gisid).toLowerCase().includes(gisidSearch)
            );

        }

        // FLOOR FILTER
        const floorsFilter = $('#floorsFilter').val();

        if (floorsFilter) {

            filteredData = filteredData.filter(item => {

                if (floorsFilter === '4') {
                    return item.number_floor >= 4;
                }

                return item.number_floor == floorsFilter;

            });

        }

        // VARIATION TYPE
        const variationType = $('#variationTypeFilter').val();

        if (variationType) {

            filteredData = filteredData.filter(item => {

                if (variationType === 'positive') {
                    return item.area_variation > 0;
                }

                if (variationType === 'negative') {
                    return item.area_variation < 0;
                }

                return true;

            });

        }

        // MIN %
        const variationMin = parseFloat($('#variationMin').val());

        if (!isNaN(variationMin)) {

            filteredData = filteredData.filter(item =>
                item.variation_percentage >= variationMin
            );

        }

        // MAX %
        const variationMax = parseFloat($('#variationMax').val());

        if (!isNaN(variationMax)) {

            filteredData = filteredData.filter(item =>
                item.variation_percentage <= variationMax
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */
        filteredData.sort((a, b) => {

            let aVal = a[currentSort.column];
            let bVal = b[currentSort.column];

            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            if (aVal < bVal) {
                return currentSort.direction === 'asc' ? -1 : 1;
            }

            if (aVal > bVal) {
                return currentSort.direction === 'asc' ? 1 : -1;
            }

            return 0;

        });

        $('#recordCount').text(filteredData.length + ' Records');

        renderTable();

    }

    /*
    |--------------------------------------------------------------------------
    | GOOGLE MAPS DIRECTION
    |--------------------------------------------------------------------------
    */
    function openGoogleMaps(coordinates, gisid) {

        if (!coordinates) {

            alert('No coordinates available');
            return;

        }

        try {

            /*
            |--------------------------------------------------------------------------
            | YOUR COORDINATES FORMAT
            | [8566253.148241518,1225035.097863368]
            |--------------------------------------------------------------------------
            */

            let coords = coordinates;

            // If coordinates are string
            if (typeof coords === 'string') {

                coords = coords
                    .replace('[', '')
                    .replace(']', '');

                coords = coords.split(',');

            }

            let x = parseFloat(coords[0]);
            let y = parseFloat(coords[1]);

            if (isNaN(x) || isNaN(y)) {

                alert('Invalid coordinates');
                return;

            }

            /*
            |--------------------------------------------------------------------------
            | CONVERT WEB MERCATOR TO LAT LNG
            |--------------------------------------------------------------------------
            */

            let lng = (x / 20037508.34) * 180;

            let lat = (y / 20037508.34) * 180;

            lat =
                180 / Math.PI *
                (
                    2 *
                    Math.atan(
                        Math.exp(lat * Math.PI / 180)
                    ) -
                    Math.PI / 2
                );

            console.log('Latitude:', lat);
            console.log('Longitude:', lng);

            /*
            |--------------------------------------------------------------------------
            | GOOGLE MAPS DIRECTION URL
            |--------------------------------------------------------------------------
            */

            let url =
                `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;

            window.open(url, '_blank');

        } catch (error) {

            console.error(error);

            alert('Unable to open map');

        }

    }

    /*
    |--------------------------------------------------------------------------
    | TABLE RENDER
    |--------------------------------------------------------------------------
    */
    function renderTable() {

        const start = (currentPage - 1) * itemsPerPage;

        const pageData =
            filteredData.slice(start, start + itemsPerPage);

        let html = '';

        pageData.forEach((item, idx) => {

            const variationClass =
                item.area_variation >= 0
                    ? 'text-success'
                    : 'text-danger';

            const variationIcon =
                item.area_variation >= 0
                    ? 'fa-arrow-up'
                    : 'fa-arrow-down';

            html += `
                <tr>

                    <td>${start + idx + 1}</td>

                    <td>
                        <span
                            class="gisid-link"
                            onclick='openGoogleMaps(${JSON.stringify(item.coordinates)}, "${item.gisid}")'
                            style="cursor:pointer; color:#1679AB; text-decoration:underline;"
                        >
                            <i class="fas fa-map-marker-alt me-1"></i>
                            ${item.gisid}
                        </span>
                    </td>

                    <td class="text-end">
                        ${parseFloat(item.sqfeet).toFixed(2)}
                    </td>

                    <td class="text-center">
                        ${item.number_floor}
                    </td>

                    <td class="text-center">
                        ${item.percentage}%
                    </td>

                    <td class="text-center">
                        ${item.basement > 0 ? item.basement : '-'}
                    </td>

                    <td class="text-end">
                        ${parseFloat(item.mis_plot_area).toFixed(2)}
                    </td>

                    <td class="text-end">
                        ${parseFloat(item.calculated_area).toFixed(2)}
                    </td>

                    <td class="text-end ${variationClass}">
                        <i class="fas ${variationIcon} me-1"></i>
                        ${item.area_variation >= 0 ? '+' : ''}
                        ${parseFloat(item.area_variation).toFixed(2)}
                    </td>

                    <td class="text-center ${variationClass}">
                        ${item.variation_percentage >= 0 ? '+' : ''}
                        ${parseFloat(item.variation_percentage).toFixed(2)}%
                    </td>

                    <td class="text-center">
                        <span class="badge ${item.assessment_count > 1 ? 'bg-info' : 'bg-secondary'}">
                            ${item.assessment_count}
                        </span>
                    </td>

                    <td class="text-center">

                        <button
                            class="btn btn-sm btn-primary"
                            onclick='openGoogleMaps(${JSON.stringify(item.coordinates)}, "${item.gisid}")'
                        >
                            <i class="fas fa-map-marked-alt"></i>
                            View Map
                        </button>

                    </td>

                </tr>
            `;

        });

        if (pageData.length === 0) {

            html = `
                <tr>
                    <td colspan="12" class="text-center py-5">
                        No records found
                    </td>
                </tr>
            `;

        }

        $('#tableBody').html(html);

    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */
    function changePage(page) {

        currentPage = page;

        renderTable();

    }

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
    .table {
        margin-bottom: 0;
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
    }
    .table thead th {
        padding: 15px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        color: #ffffff !important;
        border: none;
    }
    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: rgba(22, 121, 171, 0.05);
    }
    .gisid-link:hover {
        opacity: 0.8;
    }
    .btn-sm {
        border-radius: 8px;
        padding: 5px 12px;
    }
    .pagination .page-link {
        color: #102C57;
        border-radius: 8px;
        margin: 0 2px;
        border: none;
        padding: 8px 14px;
    }
    .pagination .active .page-link {
        background-color: #1679AB;
        color: white;
    }
    @media (max-width: 768px) {
        .dashboard-content-area { padding: 15px; }
        .table { font-size: 0.8rem; }
        .table thead th, .table tbody td { padding: 8px 10px; }
    }
</style>
@endpush
