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
                    <div class="stat-icon"><i class="fas fa-ruler-combined"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Plot Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalMisPlotArea, 2) }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> From MIS records</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Calculated Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalCalculatedArea, 2) }}</h2>
                        <small class="text-danger"><i class="fas fa-calculator"></i> Based on formula</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calculator"></i></div>
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

        <!-- Data Table -->
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
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50" selected>50 per page</option>
                        <option value="100">100 per page</option>
                        <option value="-1">All Records</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="variationsTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>GIS ID</th>
                            <th class="text-end">Sq. Feet</th>
                            <th class="text-center">Floors</th>
                            <th class="text-center">Floor %</th>
                            <th class="text-center">Basement</th>
                            <th class="text-end">MIS Area</th>
                            <th class="text-end">Calculated</th>
                            <th class="text-end">Variation</th>
                            <th class="text-center">Variation %</th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Map View</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="12" class="text-center py-5">Loading...</td></tr>
                    </tbody>
                    <tfoot id="tableFooter"></tfoot>
                彑able
            </div>

            <div id="pagination" class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2"></div>
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

    // Totals from server
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
        if (allData.length > 0) {
            console.log('Sample record with lat/lng:', allData[0]);
        }
        renderTable();
    });

    // Function to open Google Maps with actual coordinates
    function openGoogleMaps(gisid, lat, lng) {
        if (lat && lng && !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            // Use actual coordinates if available from controller
            const url = `https://www.google.com/maps?q=${lat},${lng}&z=18`;
            window.open(url, '_blank');
        } else {
            // Fallback to GIS ID search if no coordinates
            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent('Building ' + gisid)}`;
            window.open(url, '_blank');
        }
    }

    function renderTable() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const pageData = filteredData.slice(start, start + itemsPerPage);

        // Calculate totals for filtered data
        let filteredSqfeet = 0, filteredMisArea = 0, filteredCalculated = 0, filteredVariation = 0;
        filteredData.forEach(item => {
            filteredSqfeet += item.sqfeet;
            filteredMisArea += item.mis_plot_area;
            filteredCalculated += item.calculated_area;
            filteredVariation += item.area_variation;
        });
        const filteredAvgVariation = filteredMisArea > 0 ? (filteredVariation / filteredMisArea) * 100 : 0;

        // Build table body
        let html = '';
        if (pageData.length === 0) {
            html = '<tr><td colspan="12" class="text-center py-5">No records found</td></tr>';
        } else {
            pageData.forEach((item, idx) => {
                const variationClass = item.area_variation >= 0 ? 'text-success' : 'text-danger';
                const variationIcon = item.area_variation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                const hasValidCoords = item.lat && item.lng && !isNaN(item.lat) && !isNaN(item.lng) && item.lat !== 0 && item.lng !== 0;

                html += `
                    <tr>
                        <td>${start + idx + 1}</td>
                        <td>
                            <a href="javascript:void(0)" onclick="openGoogleMaps('${item.gisid}', ${item.lat || 'null'}, ${item.lng || 'null'})"
                               style="color: #1679AB; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <strong>${item.gisid}</strong>
                            </a>
                        </td>
                        <td class="text-end">${item.sqfeet.toFixed(2)}</td>
                        <td class="text-center"><span class="badge bg-primary">${item.number_floor}</span></td>
                        <td class="text-center">${item.percentage}%</td>
                        <td class="text-center">${item.basement > 0 ? item.basement : '-'}</td>
                        <td class="text-end">${item.mis_plot_area.toFixed(2)}</td>
                        <td class="text-end">${item.calculated_area.toFixed(2)}</td>
                        <td class="text-end ${variationClass}">
                            <i class="fas ${variationIcon} me-1"></i>
                            ${item.area_variation >= 0 ? '+' : ''}${item.area_variation.toFixed(2)}
                        </td>
                        <td class="text-center ${variationClass}">
                            <strong>${item.variation_percentage >= 0 ? '+' : ''}${item.variation_percentage.toFixed(2)}%</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge ${item.assessment_count > 1 ? 'bg-info' : 'bg-secondary'}">${item.assessment_count}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm ${hasValidCoords ? 'btn-primary' : 'btn-secondary'}"
                                    onclick="openGoogleMaps('${item.gisid}', ${item.lat || 'null'}, ${item.lng || 'null'})"
                                    ${!hasValidCoords ? 'disabled' : ''}>
                                <i class="fas fa-map-marked-alt"></i> View Map
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        $('#tableBody').html(html);

        // Build footer
        const footerHtml = `
            <tr style="border-top: 2px solid #dee2e6;">
                <td colspan="2"><strong>FILTERED TOTAL</strong></td>
                <td class="text-end"><strong>${filteredSqfeet.toFixed(2)}</strong></td>
                <td colspan="3"></td>
                <td class="text-end"><strong>${filteredMisArea.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${filteredCalculated.toFixed(2)}</strong></td>
                <td class="text-end ${filteredVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredVariation >= 0 ? '+' : ''}${filteredVariation.toFixed(2)}</strong>
                </td>
                <td class="text-center ${filteredAvgVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredAvgVariation >= 0 ? '+' : ''}${filteredAvgVariation.toFixed(2)}%</strong>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr style="background: #e9ecef;">
                <td colspan="2"><strong>WARD TOTAL</strong></td>
                <td class="text-end"><strong>${serverTotals.totalSqfeet.toFixed(2)}</strong></td>
                <td colspan="3"></td>
                <td class="text-end"><strong>${serverTotals.totalMisPlotArea.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${serverTotals.totalCalculatedArea.toFixed(2)}</strong></td>
                <td class="text-end ${serverTotals.totalAreaVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.totalAreaVariation >= 0 ? '+' : ''}${serverTotals.totalAreaVariation.toFixed(2)}</strong>
                </td>
                <td class="text-center ${serverTotals.avgVariationPercentage >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.avgVariationPercentage >= 0 ? '+' : ''}${serverTotals.avgVariationPercentage.toFixed(2)}%</strong>
                </td>
                <td colspan="2"></td>
            </tr>
        `;
        $('#tableFooter').html(footerHtml);

        // Render pagination
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1 && filteredData.length <= itemsPerPage) {
            $('#pagination').html(`
                <div class="text-muted">
                    Showing ${filteredData.length} of ${filteredData.length} records
                </div>
            `);
            return;
        }

        const startRecord = (currentPage - 1) * itemsPerPage + 1;
        const endRecord = Math.min(currentPage * itemsPerPage, filteredData.length);

        let paginationHtml = `
            <div class="text-muted">
                Showing ${startRecord} to ${endRecord} of ${filteredData.length} records
            </div>
            <nav>
                <ul class="pagination mb-0">
        `;

        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">« Previous</a>
            </li>
        `;

        for (let i = 1; i <= Math.min(totalPages, 10); i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
        }

        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next »</a>
            </li>
        `;

        paginationHtml += `</ul></nav>`;
        $('#pagination').html(paginationHtml);
    }

    function changePage(page) {
        if (page < 1 || page > Math.ceil(filteredData.length / itemsPerPage)) return;
        currentPage = page;
        renderTable();
    }

    $('#perPageSelect').change(function() {
        const val = $(this).val();
        itemsPerPage = val === '-1' ? allData.length : parseInt(val);
        currentPage = 1;
        renderTable();
    });

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
            'Latitude': item.lat || '',
            'Longitude': item.lng || '',
            'Original Coordinates': item.coordinates
        }));

        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Building_Variations');
        XLSX.writeFile(wb, `Ward_${Date.now()}_Building_Variations.xlsx`);
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
</style>
@endpush
