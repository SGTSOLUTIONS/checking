{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward ' . $warddetail->ward_no . ' - Area Variations')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0">
                        Ward {{ $warddetail->ward_no }} -
                        {{ ucfirst($warddetail->zone) }} Zone
                    </h5>
                    <small>Area Variation Analysis Report</small>
                </div>

                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-light btn-sm">
                        <i class="fas fa-print"></i> Print
                    </button>

                    <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Corporation ID:</strong>
                    {{ $warddetail->corporation_id }}
                </div>

                <div class="col-md-3">
                    <strong>Ward Number:</strong>
                    {{ $warddetail->ward_no }}
                </div>

                <div class="col-md-3">
                    <strong>Zone:</strong>
                    {{ ucfirst($warddetail->zone) }}
                </div>

                <div class="col-md-3">
                    <strong>Total Buildings:</strong>
                    {{ count($result) }}
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY --}}
    @php
        $totalDroneArea = collect($result)->sum('calculated_area');
        $totalMisArea = collect($result)->sum('mis_plot_area');
        $totalDifference = $totalDroneArea - $totalMisArea;

        $excessCount = collect($result)->where('area_variation', '>', 100)->count();

        $shortCount = collect($result)->where('area_variation', '<', -100)->count();

        $matchedCount = collect($result)->filter(function ($item) {
            return $item['area_variation'] >= -100 &&
                   $item['area_variation'] <= 100;
        })->count();

        $positiveVariation = collect($result)
            ->where('area_variation', '>', 0)
            ->sum('area_variation');

        $negativeVariation = collect($result)
            ->where('area_variation', '<', 0)
            ->sum('area_variation');
    @endphp

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card bg-info text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6>Total Calculated Area</h6>
                    <h4 class="mb-0">
                        {{ number_format($totalDroneArea, 2) }}
                    </h4>
                    <small>sq.ft</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6>Total MIS Area</h6>
                    <h4 class="mb-0">
                        {{ number_format($totalMisArea, 2) }}
                    </h4>
                    <small>sq.ft</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card
                {{ $totalDifference > 0 ? 'bg-danger' : ($totalDifference < 0 ? 'bg-success' : 'bg-secondary') }}
                text-white border-0 shadow-sm">

                <div class="card-body">
                    <h6>Total Difference</h6>

                    <h4 class="mb-0">
                        {{ number_format(abs($totalDifference), 2) }}
                    </h4>

                    <small>
                        {{ $totalDifference > 0 ? 'EXCESS' : ($totalDifference < 0 ? 'SHORT' : 'MATCHED') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6>Variation Range</h6>

                    <div>
                        +{{ number_format($positiveVariation, 2) }}
                    </div>

                    <div>
                        -{{ number_format(abs($negativeVariation), 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Area Status</label>

                    <select id="areaFilter" class="form-select">
                        <option value="all">All</option>
                        <option value="excess">EXCESS</option>
                        <option value="short">SHORT</option>
                        <option value="matched">MATCHED</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort By</label>

                    <select id="sortBy" class="form-select">
                        <option value="gisid">GIS ID</option>
                        <option value="variation_high">Variation High → Low</option>
                        <option value="variation_low">Variation Low → High</option>
                        <option value="percentage_high">Percentage High → Low</option>
                        <option value="percentage_low">Percentage Low → High</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Search GIS ID</label>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Enter GIS ID..."
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>

                    <button
                        class="btn btn-secondary w-100"
                        onclick="resetFilters()"
                    >
                        Reset
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- CHART --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h6 class="mb-0">Variation Distribution</h6>
        </div>

        <div class="card-body">
            <canvas id="variationChart" height="90"></canvas>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0">Detailed Building Analysis</h6>

            <div>
                <select id="perPage" class="form-select form-select-sm">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle" id="variationsTable">

                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>GIS ID</th>
                            <th>Polygon</th>
                            <th>Floors</th>
                            <th>%</th>
                            <th>Basement</th>
                            <th>Calculated</th>
                            <th>MIS Area</th>
                            <th>Difference</th>
                            <th>Variation %</th>
                            <th>Status</th>
                            <th>Assessment</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody"></tbody>

                </table>

            </div>

            <div class="row mt-3">

                <div class="col-md-6" id="paginationInfo"></div>

                <div class="col-md-6">
                    <ul class="pagination justify-content-end" id="pagination"></ul>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>

const resultsData = @json($result);

let currentPage = 1;
let perPage = 25;
let currentAreaFilter = 'all';
let currentSortBy = 'gisid';
let currentSearch = '';

const currentWardNo = "{{ $warddetail->ward_no }}";

function formatNumber(num) {

    if (num === null || num === undefined || isNaN(num)) {
        return '0.00';
    }

    return Number(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function getFilteredData() {

    let filtered = [...resultsData];

    // Filter
    if (currentAreaFilter !== 'all') {

        filtered = filtered.filter(item => {

            if (currentAreaFilter === 'excess') {
                return item.area_variation > 100;
            }

            if (currentAreaFilter === 'short') {
                return item.area_variation < -100;
            }

            if (currentAreaFilter === 'matched') {
                return Math.abs(item.area_variation) <= 100;
            }

            return true;
        });
    }

    // Search
    if (currentSearch) {

        filtered = filtered.filter(item =>
            item.gisid.toString().toLowerCase()
                .includes(currentSearch.toLowerCase())
        );
    }

    // Sorting
    filtered.sort((a, b) => {

        switch (currentSortBy) {

            case 'variation_high':
                return Math.abs(b.area_variation) - Math.abs(a.area_variation);

            case 'variation_low':
                return Math.abs(a.area_variation) - Math.abs(b.area_variation);

            case 'percentage_high':
                return b.variation_percentage - a.variation_percentage;

            case 'percentage_low':
                return a.variation_percentage - b.variation_percentage;

            default:
                return a.gisid.localeCompare(b.gisid);
        }
    });

    return filtered;
}

function renderTable() {

    const filtered = getFilteredData();

    const tbody = document.getElementById('tableBody');

    tbody.innerHTML = '';

    const displayAll = perPage === 'all';

    const itemsPerPage = displayAll
        ? filtered.length
        : parseInt(perPage);

    const totalItems = filtered.length;

    const totalPages = displayAll
        ? 1
        : Math.ceil(totalItems / itemsPerPage);

    if (currentPage > totalPages) {
        currentPage = 1;
    }

    const start = (currentPage - 1) * itemsPerPage;

    const pageData = filtered.slice(start, start + itemsPerPage);

    if (pageData.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center">
                    No Records Found
                </td>
            </tr>
        `;

        return;
    }

    pageData.forEach((result, index) => {

        let rowClass = '';
        let badge = '';

        if (result.area_variation > 100) {

            rowClass = 'table-danger';
            badge = '<span class="badge bg-danger">EXCESS</span>';

        } else if (result.area_variation < -100) {

            rowClass = 'table-success';
            badge = '<span class="badge bg-success">SHORT</span>';

        } else {

            rowClass = 'table-info';
            badge = '<span class="badge bg-info">MATCHED</span>';
        }

        tbody.innerHTML += `
            <tr class="${rowClass}">
                <td>${start + index + 1}</td>

                <td>
                    <strong>${result.gisid}</strong>
                </td>

                <td>${formatNumber(result.sqfeet)}</td>

                <td>${result.number_floor}</td>

                <td>${result.percentage}%</td>

                <td>
                    ${result.basement > 0 ? result.basement : '-'}
                </td>

                <td>
                    <strong>
                        ${formatNumber(result.calculated_area)}
                    </strong>
                </td>

                <td>${formatNumber(result.mis_plot_area)}</td>

                <td class="${result.area_variation > 0 ? 'text-danger' : 'text-success'}">
                    <strong>
                        ${result.area_variation > 0 ? '+' : ''}
                        ${formatNumber(result.area_variation)}
                    </strong>
                </td>

                <td>
                    ${result.variation_percentage > 0 ? '+' : ''}
                    ${formatNumber(result.variation_percentage)}%
                </td>

                <td>${badge}</td>

                <td>
                    <span class="badge bg-secondary">
                        ${result.assessment_count}
                    </span>

                    <button
                        class="btn btn-sm btn-outline-primary ms-1"
                        onclick="showDetails('${result.gisid}')"
                    >
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    renderPagination(totalPages, totalItems, itemsPerPage);
}

function renderPagination(totalPages, totalItems, itemsPerPage) {

    const pagination = document.getElementById('pagination');

    const paginationInfo = document.getElementById('paginationInfo');

    const start = totalItems === 0
        ? 0
        : ((currentPage - 1) * itemsPerPage) + 1;

    const end = Math.min(currentPage * itemsPerPage, totalItems);

    paginationInfo.innerHTML =
        `Showing ${start} to ${end} of ${totalItems} entries`;

    if (totalPages <= 1) {

        pagination.innerHTML = '';

        return;
    }

    let html = '';

    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
               onclick="changePage(${currentPage - 1})">
               Previous
            </a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {

        html += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link"
                   href="javascript:void(0)"
                   onclick="changePage(${i})">
                    ${i}
                </a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link"
               href="javascript:void(0)"
               onclick="changePage(${currentPage + 1})">
               Next
            </a>
        </li>
    `;

    pagination.innerHTML = html;
}

function changePage(page) {

    currentPage = page;

    renderTable();
}

function resetFilters() {

    currentAreaFilter = 'all';
    currentSortBy = 'gisid';
    currentSearch = '';
    currentPage = 1;

    document.getElementById('areaFilter').value = 'all';
    document.getElementById('sortBy').value = 'gisid';
    document.getElementById('searchInput').value = '';

    renderTable();
}

function showDetails(gisid) {

    const result = resultsData.find(item => item.gisid == gisid);

    if (!result) return;

    alert(
        `GIS ID: ${result.gisid}\n\n` +
        `Calculated Area: ${formatNumber(result.calculated_area)} sq.ft\n` +
        `MIS Area: ${formatNumber(result.mis_plot_area)} sq.ft\n` +
        `Difference: ${formatNumber(result.area_variation)} sq.ft\n` +
        `Variation: ${formatNumber(result.variation_percentage)}%`
    );
}

function exportToExcel() {

    const filtered = getFilteredData();

    const exportData = filtered.map(item => ({
        'GIS ID': item.gisid,
        'Polygon Area': item.sqfeet,
        'Floors': item.number_floor,
        'Percentage': item.percentage,
        'Basement': item.basement,
        'Calculated Area': item.calculated_area,
        'MIS Area': item.mis_plot_area,
        'Difference': item.area_variation,
        'Variation %': item.variation_percentage,
        'Assessment Count': item.assessment_count
    }));

    const ws = XLSX.utils.json_to_sheet(exportData);

    const wb = XLSX.utils.book_new();

    XLSX.utils.book_append_sheet(wb, ws, 'Variations');

    XLSX.writeFile(
        wb,
        `ward_${currentWardNo}_variations.xlsx`
    );
}

function initChart() {

    const ctx = document.getElementById('variationChart');

    const ranges = {
        'Excess': 0,
        'Matched': 0,
        'Short': 0
    };

    resultsData.forEach(item => {

        if (item.area_variation > 100) {

            ranges.Excess++;

        } else if (item.area_variation < -100) {

            ranges.Short++;

        } else {

            ranges.Matched++;
        }
    });

    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: Object.keys(ranges),

            datasets: [{
                label: 'Buildings',
                data: Object.values(ranges),
                backgroundColor: [
                    '#dc3545',
                    '#0dcaf0',
                    '#198754'
                ]
            }]
        },

        options: {
            responsive: true
        }
    });
}

// EVENTS

document.getElementById('areaFilter')
.addEventListener('change', function(e) {

    currentAreaFilter = e.target.value;
    currentPage = 1;

    renderTable();
});

document.getElementById('sortBy')
.addEventListener('change', function(e) {

    currentSortBy = e.target.value;
    currentPage = 1;

    renderTable();
});

document.getElementById('searchInput')
.addEventListener('keyup', function(e) {

    currentSearch = e.target.value;
    currentPage = 1;

    renderTable();
});

document.getElementById('perPage')
.addEventListener('change', function(e) {

    perPage = e.target.value;
    currentPage = 1;

    renderTable();
});

document.addEventListener('DOMContentLoaded', function() {

    initChart();

    renderTable();
});

</script>

@endpush
