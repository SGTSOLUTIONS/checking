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

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalBuildings ?? 0) }}</h2>
                        <small class="text-info"><i class="fas fa-building"></i> In this ward</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Sq. Feet</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalSqfeet ?? 0, 2) }}</h2>
                        <small class="text-info"><i class="fas fa-vector-square"></i> Built-up area</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-ruler-combined"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Plot Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalMisPlotArea ?? 0, 2) }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> From MIS records</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Calculated Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalCalculatedArea ?? 0, 2) }}</h2>
                        <small class="text-danger"><i class="fas fa-calculator"></i> Based on formula</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                </div>
            </div>
        </div>

        <!-- Variation Summary Card -->
        @php
            $totalAreaVariation = $totalAreaVariation ?? 0;
            $avgVariationPercentage = $avgVariationPercentage ?? 0;
            $variationColor = $totalAreaVariation >= 0 ? 'success' : 'danger';
            $variationIcon = $totalAreaVariation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            $variationText = $totalAreaVariation >= 0 ? 'Under-assessment' : 'Over-assessment';
        @endphp

        <div class="stat-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-12">
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
                        <option value="positive">Under-assessment (Positive)</option>
                        <option value="negative">Over-assessment (Negative)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Min Variation %</label>
                    <input type="number" id="variationMin" class="form-control" placeholder="Min %" step="any">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Max Variation %</label>
                    <input type="number" id="variationMax" class="form-control" placeholder="Max %" step="any">
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-semibold small">&nbsp;</label>
                    <button class="btn btn-primary w-100" id="applyFiltersBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
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
                    <label class="me-2">Show:</label>
                    <select id="perPageSelect" class="form-select form-select-sm d-inline-block w-auto">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="index">S.No <i class="fas fa-sort"></i></th>
                            <th class="sortable" data-column="gisid">GIS ID <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="sqfeet">Sq. Feet <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="number_floor">Floors <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="percentage">Floor % <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="mis_plot_area">MIS Area <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="calculated_area">Calculated <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="area_variation">Variation <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="variation_percentage">Var % <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="half_year_tax">Half Year Tax <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="tax_balance">Balance <i class="fas fa-sort"></i></th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Map View</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="13" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading data...</p>
                        </td></tr>
                    </tbody>
                    <tfoot id="tableFooter"></tfoot>
                </table>
            </div>

            <div id="pagination" class="d-flex justify-content-between align-items-center mt-4 flex-wrap"></div>
        </div>

        <!-- Info Note -->
        <div class="stat-card p-3 mt-4" style="background: #e7f3ff;">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3" style="color:#1679AB;"></i>
                <div>
                    <strong>Note:</strong>
                    Click on <strong>GIS ID</strong> or <strong>View Map</strong> to see location on Google Maps.
                    <span class="ms-3"><span class="badge bg-success">Positive</span> = Under-assessment</span>
                    <span class="ms-3"><span class="badge bg-danger">Negative</span> = Over-assessment</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    let allData = @json($allDataJson ?? []);

    if (typeof allData === 'string') {
        allData = JSON.parse(allData);
    }

    let filteredData = [...allData];
    let currentPage = 1;
    let itemsPerPage = 50;
    let currentSort = { column: 'index', direction: 'asc' };

    const serverTotals = {
        totalSqfeet: {{ $totalSqfeet ?? 0 }},
        totalMisPlotArea: {{ $totalMisPlotArea ?? 0 }},
        totalCalculatedArea: {{ $totalCalculatedArea ?? 0 }},
        totalAreaVariation: {{ $totalAreaVariation ?? 0 }},
        avgVariationPercentage: {{ $avgVariationPercentage ?? 0 }}
    };

    $(document).ready(function() {
        initializeFilters();
        applyFiltersAndRender();
    });

    function initializeFilters() {
        $('#applyFiltersBtn').click(function() {
            currentPage = 1;
            applyFiltersAndRender();
        });

        $('#clearFiltersBtn').click(function() {
            $('#gisidSearch, #floorsFilter, #variationTypeFilter, #variationMin, #variationMax').val('');
            currentPage = 1;
            applyFiltersAndRender();
        });

        $('#perPageSelect').change(function() {
            itemsPerPage = $(this).val() === '-1' ? filteredData.length : parseInt($(this).val());
            currentPage = 1;
            renderTable();
        });

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

    function applyFiltersAndRender() {
        filteredData = [...allData];

        const gisidSearch = $('#gisidSearch').val().toLowerCase().trim();
        if (gisidSearch) {
            filteredData = filteredData.filter(item => item.gisid.toLowerCase().includes(gisidSearch));
        }

        const floorsFilter = $('#floorsFilter').val();
        if (floorsFilter) {
            filteredData = filteredData.filter(item => {
                if (floorsFilter === '4') return item.number_floor >= 4;
                return item.number_floor == floorsFilter;
            });
        }

        const variationType = $('#variationTypeFilter').val();
        if (variationType === 'positive') {
            filteredData = filteredData.filter(item => item.area_variation > 0);
        } else if (variationType === 'negative') {
            filteredData = filteredData.filter(item => item.area_variation < 0);
        }

        const variationMin = parseFloat($('#variationMin').val());
        if (!isNaN(variationMin)) {
            filteredData = filteredData.filter(item => item.variation_percentage >= variationMin);
        }

        const variationMax = parseFloat($('#variationMax').val());
        if (!isNaN(variationMax)) {
            filteredData = filteredData.filter(item => item.variation_percentage <= variationMax);
        }

        filteredData.sort((a, b) => {
            let aVal = a[currentSort.column];
            let bVal = b[currentSort.column];
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        $('#recordCount').text(filteredData.length + ' Records');
        currentPage = 1;
        renderTable();
    }

    function openGoogleMaps(gisid, lat, lng) {
        let url;
        if (lat && lng && !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            url = `https://www.google.com/maps?q=${lat},${lng}&z=18`;
        } else {
            url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent('Building ' + gisid)}`;
        }
        window.open(url, '_blank');
    }

    function renderTable() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const pageData = filteredData.slice(start, start + itemsPerPage);

        let filteredSqfeet = 0, filteredMisArea = 0, filteredCalculated = 0, filteredVariation = 0;
        let filteredTax = 0, filteredBalance = 0;

        filteredData.forEach(item => {
            filteredSqfeet += item.sqfeet;
            filteredMisArea += item.mis_plot_area;
            filteredCalculated += item.calculated_area;
            filteredVariation += item.area_variation;
            filteredTax += item.half_year_tax || 0;
            filteredBalance += item.tax_balance || 0;
        });

        let html = '';
        if (pageData.length === 0) {
            html = '<tr><td colspan="13" class="text-center py-5">No records found</td><tr>';
        } else {
            pageData.forEach((item, idx) => {
                const variationClass = item.area_variation >= 0 ? 'text-success' : 'text-danger';
                const variationIcon = item.area_variation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                const hasValidCoords = item.hasValidCoords === true;

                html += `<tr>
                    <td>${start + idx + 1}</td>
                    <td>
                        <a href="javascript:void(0)" onclick="openGoogleMaps('${item.gisid}', ${item.lat || 'null'}, ${item.lng || 'null'})"
                           style="color: #1679AB; text-decoration: none;">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <strong>${item.gisid}</strong>
                        </a>
                    </td>
                    <td class="text-end">${(item.sqfeet || 0).toFixed(2)}</td>
                    <td class="text-center"><span class="badge bg-primary">${item.number_floor || 0}</span></td>
                    <td class="text-center">${item.percentage || 0}%</td>
                    <td class="text-end">${(item.mis_plot_area || 0).toFixed(2)}</td>
                    <td class="text-end">${(item.calculated_area || 0).toFixed(2)}</td>
                    <td class="text-end ${variationClass}">
                        <i class="fas ${variationIcon} me-1"></i>
                        ${(item.area_variation || 0) >= 0 ? '+' : ''}${(item.area_variation || 0).toFixed(2)}
                    </td>
                    <td class="text-center ${variationClass}">
                        <strong>${(item.variation_percentage || 0) >= 0 ? '+' : ''}${(item.variation_percentage || 0).toFixed(2)}%</strong>
                    </td>
                    <td class="text-end">${(item.half_year_tax || 0).toFixed(2)}</td>
                    <td class="text-end ${(item.tax_balance || 0) > 0 ? 'text-danger' : 'text-success'}">
                        ${(item.tax_balance || 0).toFixed(2)}
                    </td>
                    <td class="text-center">
                        <span class="badge ${(item.assessment_count || 0) > 1 ? 'bg-info' : 'bg-secondary'}">${item.assessment_count || 0}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm ${hasValidCoords ? 'btn-primary' : 'btn-secondary'}"
                                onclick="openGoogleMaps('${item.gisid}', ${item.lat || 'null'}, ${item.lng || 'null'})"
                                ${!hasValidCoords ? 'disabled' : ''}>
                            <i class="fas fa-map-marked-alt"></i> View Map
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tableBody').html(html);

        const filteredAvgVariation = filteredMisArea > 0 ? (filteredVariation / filteredMisArea) * 100 : 0;

        $('#tableFooter').html(`
            <tr style="border-top: 2px solid #dee2e6; background: #f8f9fa;">
                <td colspan="2"><strong>FILTERED TOTAL</strong></td>
                <td class="text-end"><strong>${filteredSqfeet.toFixed(2)}</strong></td>
                <td colspan="2"></td>
                <td class="text-end"><strong>${filteredMisArea.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${filteredCalculated.toFixed(2)}</strong></td>
                <td class="text-end ${filteredVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredVariation >= 0 ? '+' : ''}${filteredVariation.toFixed(2)}</strong>
                </td>
                <td class="text-center ${filteredAvgVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredAvgVariation >= 0 ? '+' : ''}${filteredAvgVariation.toFixed(2)}%</strong>
                </td>
                <td class="text-end"><strong>${filteredTax.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${filteredBalance.toFixed(2)}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr style="background: #e9ecef;">
                <td colspan="2"><strong>WARD TOTAL</strong></td>
                <td class="text-end"><strong>${serverTotals.totalSqfeet.toFixed(2)}</strong></td>
                <td colspan="2"></td>
                <td class="text-end"><strong>${serverTotals.totalMisPlotArea.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${serverTotals.totalCalculatedArea.toFixed(2)}</strong></td>
                <td class="text-end ${serverTotals.totalAreaVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.totalAreaVariation >= 0 ? '+' : ''}${serverTotals.totalAreaVariation.toFixed(2)}</strong>
                </td>
                <td class="text-center ${serverTotals.avgVariationPercentage >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.avgVariationPercentage >= 0 ? '+' : ''}${serverTotals.avgVariationPercentage.toFixed(2)}%</strong>
                </td>
                <td colspan="4"></td>
            </tr>
        `);

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1 && filteredData.length <= itemsPerPage) {
            $('#pagination').html(`<div class="text-muted">Showing ${filteredData.length} of ${filteredData.length} records</div>`);
            return;
        }

        const startRecord = (currentPage - 1) * itemsPerPage + 1;
        const endRecord = Math.min(currentPage * itemsPerPage, filteredData.length);

        let html = `<div class="text-muted">Showing ${startRecord} to ${endRecord} of ${filteredData.length} records</div>
                    <nav><ul class="pagination mb-0">`;
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">« Previous</a>
                 </li>`;

        for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                     </li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next »</a>
                 </li></ul></nav>`;
        $('#pagination').html(html);
    }

    function changePage(page) {
        if (page < 1 || page > Math.ceil(filteredData.length / itemsPerPage)) return;
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
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead th {
        padding: 15px;
        font-weight: 600;
        font-size: 0.85rem;
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        color: white !important;
        border: none;
        white-space: nowrap;
        cursor: pointer;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background: rgba(22, 121, 171, 0.05);
    }

    .btn-sm {
        border-radius: 8px;
        padding: 5px 12px;
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
        background: #1679AB;
        color: white;
    }

    .badge {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 20px;
    }

    @media (max-width: 768px) {
        .dashboard-content-area {
            padding: 15px;
        }
        .table {
            font-size: 0.75rem;
        }
        .table thead th,
        .table tbody td {
            padding: 8px 10px;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
    }

    @media print {
        .btn, .pagination, #perPageSelect, .filters-section, #clearFiltersBtn, #applyFiltersBtn {
            display: none !important;
        }
        .dashboard-content-area {
            background: white;
            padding: 10px;
        }
    }
</style>
@endpush
