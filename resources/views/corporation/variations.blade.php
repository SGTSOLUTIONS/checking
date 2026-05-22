{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.app')

@section('title', 'Ward ' . $ward_no . ' - Area & Usage Variations')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Ward {{ $warddetail->ward_no }} - {{ $warddetail->zone }} Zone
                            <small class="text-white-50">Variation Analysis Report</small>
                        </h5>
                        <div>
                            <button onclick="window.print()" class="btn btn-light btn-sm">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Corporation ID:</strong> {{ $warddetail->corporation_id }}
                        </div>
                        <div class="col-md-3">
                            <strong>Ward Number:</strong> {{ $warddetail->ward_no }}
                        </div>
                        <div class="col-md-3">
                            <strong>Zone:</strong> {{ ucfirst($warddetail->zone) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Buildings:</strong> {{ count($results) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    @php
        $totalDroneArea = collect($results)->sum('drone_area');
        $totalMisArea = collect($results)->sum('mis_total_area');
        $totalDifference = $totalDroneArea - $totalMisArea;
        $excessCount = collect($results)->where('area_variation', 'EXCESS')->count();
        $shortCount = collect($results)->where('area_variation', 'SHORT')->count();
        $matchedCount = collect($results)->where('area_variation', 'MATCHED')->count();
        $usageVariationCount = collect($results)->where('usage_variation', true)->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Drone Area</h6>
                    <h3 class="mb-0">{{ number_format($totalDroneArea, 2) }} sq.ft</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Total MIS Area</h6>
                    <h3 class="mb-0">{{ number_format($totalMisArea, 2) }} sq.ft</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $totalDifference > 0 ? 'bg-danger' : 'bg-success' }} text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Difference</h6>
                    <h3 class="mb-0">{{ number_format($totalDifference, 2) }} sq.ft</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6 class="card-title">Status</h6>
                    <h3 class="mb-0">
                        @if($totalDifference > 0)
                            OVER
                        @elseif($totalDifference < 0)
                            UNDER
                        @else
                            MATCHED
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Variation Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Area Variation Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="bg-danger text-white p-3 rounded">
                                <h4 class="mb-0">{{ $excessCount }}</h4>
                                <small>EXCESS (>100 sq.ft)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-success text-white p-3 rounded">
                                <h4 class="mb-0">{{ $shortCount }}</h4>
                                <small>SHORT (<-100 sq.ft)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-info text-white p-3 rounded">
                                <h4 class="mb-0">{{ $matchedCount }}</h4>
                                <small>MATCHED</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Usage Variation Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-warning text-dark p-3 rounded">
                                <h4 class="mb-0">{{ $usageVariationCount }}</h4>
                                <small>Buildings with Usage Mismatch</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success text-white p-3 rounded">
                                <h4 class="mb-0">{{ count($results) - $usageVariationCount }}</h4>
                                <small>Buildings with Matching Usage</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Filter by Area Status:</label>
                            <select id="areaFilter" class="form-select">
                                <option value="all">All</option>
                                <option value="EXCESS">EXCESS</option>
                                <option value="SHORT">SHORT</option>
                                <option value="MATCHED">MATCHED</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Filter by Usage Status:</label>
                            <select id="usageFilter" class="form-select">
                                <option value="all">All</option>
                                <option value="mismatch">Mismatch</option>
                                <option value="matched">Matched</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Search:</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="GIS ID, Building Name, Road...">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button onclick="resetFilters()" class="btn btn-secondary form-control">Reset Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results Table with Pagination -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Building-wise Detailed Analysis</h6>
                        <div>
                            <label>Show
                                <select id="perPage" class="form-select form-select-sm d-inline-block w-auto">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                entries
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="variationsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 10%">GIS ID</th>
                                    <th style="width: 15%">Building Name</th>
                                    <th style="width: 15%">Road Name</th>
                                    <th style="width: 10%">Building Usage</th>
                                    <th style="width: 8%">Floors</th>
                                    <th style="width: 10%">Drone Area</th>
                                    <th style="width: 10%">MIS Area</th>
                                    <th style="width: 8%">Difference</th>
                                    <th style="width: 8%">Area Status</th>
                                    <th style="width: 8%">Usage Status</th>
                                    <th style="width: 8%">Assessments</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- JavaScript will populate this -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-3">
                        <div class="col-md-6" id="paginationInfo"></div>
                        <div class="col-md-6">
                            <nav>
                                <ul class="pagination justify-content-end" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals Container -->
<div id="modalsContainer"></div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// Convert PHP data to JavaScript
const resultsData = @json($results);
let currentPage = 1;
let perPage = 25;
let currentAreaFilter = 'all';
let currentUsageFilter = 'all';
let currentSearch = '';

// Filter and paginate data
function getFilteredData() {
    let filtered = resultsData;

    // Area filter
    if (currentAreaFilter !== 'all') {
        filtered = filtered.filter(item => item.area_variation === currentAreaFilter);
    }

    // Usage filter
    if (currentUsageFilter === 'mismatch') {
        filtered = filtered.filter(item => item.usage_variation === true);
    } else if (currentUsageFilter === 'matched') {
        filtered = filtered.filter(item => item.usage_variation === false);
    }

    // Search
    if (currentSearch) {
        const searchLower = currentSearch.toLowerCase();
        filtered = filtered.filter(item =>
            item.gisid.toString().includes(searchLower) ||
            item.building_name.toLowerCase().includes(searchLower) ||
            item.road_name.toLowerCase().includes(searchLower)
        );
    }

    return filtered;
}

function renderTable() {
    const filtered = getFilteredData();
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / perPage);

    // Ensure current page is valid
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const pageData = filtered.slice(start, end);

    // Render table body
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    pageData.forEach((result, index) => {
        const row = tbody.insertRow();
        const serialNo = start + index + 1;

        row.innerHTML = `
            <td>${serialNo}</td>
            <td><strong>${result.gisid}</strong></td>
            <td>${result.building_name || 'N/A'}</td>
            <td>${result.road_name || 'N/A'}</td>
            <td>${capitalize(result.building_usage || 'N/A')}</td>
            <td>
                <span class="badge bg-secondary">F: ${result.number_floor}</span>
                ${result.basement > 0 ? `<span class="badge bg-info">B: ${result.basement}</span>` : ''}
                ${result.percentage > 0 ? `<span class="badge bg-warning">${result.percentage}%</span>` : ''}
            </td>
            <td>${formatNumber(result.drone_area)}</td>
            <td>${formatNumber(result.mis_total_area)}</td>
            <td class="${result.area_difference > 0 ? 'text-danger' : (result.area_difference < 0 ? 'text-success' : '')}">
                ${formatNumber(result.area_difference)}
            </td>
            <td>${getAreaBadge(result.area_variation)}</td>
            <td>${getUsageBadge(result.usage_variation)}</td>
            <td>${result.assessment_count}</td>
            <td>
                <button type="button" class="btn btn-sm btn-info" onclick="showDetails(${serialNo - 1})">
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
        `;
    });

    // Render pagination
    renderPagination(totalPages, totalItems);

    // Store page data for modal access
    window.currentPageData = pageData;
}

function renderPagination(totalPages, totalItems) {
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');

    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(start + perPage - 1, totalItems);

    paginationInfo.innerHTML = `Showing ${start} to ${end} of ${totalItems} entries`;

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Previous button
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
    </li>`;

    // Page numbers
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);

    if (endPage - startPage + 1 < maxVisible) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1)">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
            <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
        </li>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
    }

    // Next button
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
    </li>`;

    pagination.innerHTML = html;
}

function changePage(page) {
    if (page < 1) return;
    const filtered = getFilteredData();
    const totalPages = Math.ceil(filtered.length / perPage);
    if (page > totalPages) return;
    currentPage = page;
    renderTable();
}

// Helper functions
function formatNumber(num) {
    return Number(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

function getAreaBadge(status) {
    const badges = {
        'EXCESS': '<span class="badge bg-danger">EXCESS</span>',
        'SHORT': '<span class="badge bg-success">SHORT</span>',
        'MATCHED': '<span class="badge bg-info">MATCHED</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">UNKNOWN</span>';
}

function getUsageBadge(variation) {
    return variation ?
        '<span class="badge bg-warning text-dark">MISMATCH</span>' :
        '<span class="badge bg-success">MATCHED</span>';
}

// Event listeners
document.getElementById('areaFilter').addEventListener('change', function(e) {
    currentAreaFilter = e.target.value;
    currentPage = 1;
    renderTable();
});

document.getElementById('usageFilter').addEventListener('change', function(e) {
    currentUsageFilter = e.target.value;
    currentPage = 1;
    renderTable();
});

document.getElementById('searchInput').addEventListener('keyup', function(e) {
    currentSearch = e.target.value;
    currentPage = 1;
    renderTable();
});

document.getElementById('perPage').addEventListener('change', function(e) {
    perPage = parseInt(e.target.value);
    currentPage = 1;
    renderTable();
});

function resetFilters() {
    document.getElementById('areaFilter').value = 'all';
    document.getElementById('usageFilter').value = 'all';
    document.getElementById('searchInput').value = '';
    currentAreaFilter = 'all';
    currentUsageFilter = 'all';
    currentSearch = '';
    currentPage = 1;
    renderTable();
}

// Modal functions
function showDetails(index) {
    const result = window.currentPageData[index];
    if (!result) return;

    const modalHtml = `
        <div class="modal fade" id="detailsModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">GIS ID: ${result.gisid} - ${result.building_name || 'N/A'}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Building Details -->
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">Building Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>GIS ID:</strong> ${result.gisid}<br>
                                        <strong>Building Name:</strong> ${result.building_name || 'N/A'}<br>
                                        <strong>Road Name:</strong> ${result.road_name || 'N/A'}<br>
                                        <strong>Building Usage:</strong> ${capitalize(result.building_usage || 'N/A')}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Polygon Area:</strong> ${formatNumber(result.sqfeet)} sq.ft<br>
                                        <strong>Number of Floors:</strong> ${result.number_floor}<br>
                                        <strong>Basement:</strong> ${result.basement}<br>
                                        <strong>Percentage:</strong> ${result.percentage}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Area Variation -->
                        <div class="card mb-3">
                            <div class="card-header ${result.area_variation === 'EXCESS' ? 'bg-danger' : (result.area_variation === 'SHORT' ? 'bg-success' : 'bg-info')} text-white">
                                <h6 class="mb-0">Area Variation Analysis</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <strong>Drone Area:</strong><br>
                                        <span class="h5">${formatNumber(result.drone_area)} sq.ft</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>MIS Area:</strong><br>
                                        <span class="h5">${formatNumber(result.mis_total_area)} sq.ft</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Difference:</strong><br>
                                        <span class="h5 ${result.area_difference > 0 ? 'text-danger' : (result.area_difference < 0 ? 'text-success' : '')}">
                                            ${formatNumber(result.area_difference)} sq.ft
                                        </span><br>
                                        ${getAreaBadge(result.area_variation)}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Mismatches -->
                        ${result.usage_variation && result.usage_mismatches.length > 0 ? `
                        <div class="card mb-3">
                            <div class="card-header bg-warning">
                                <h6 class="mb-0">Usage Mismatches</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr><th>Assessment</th><th>Survey Usage</th><th>MIS Usage</th></tr>
                                        </thead>
                                        <tbody>
                                            ${result.usage_mismatches.map(m => `
                                                <tr class="table-warning">
                                                    <td>${m.assessment}</td>
                                                    <td>${capitalize(m.survey_usage || 'N/A')}</td>
                                                    <td>${capitalize(m.mis_usage || 'N/A')}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        ` : ''}

                        <!-- Assessments -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Assessment Details (${result.assessment_count} records)</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 400px;">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Assessment</th><th>Owner Name</th><th>Plot Area</th><th>Half Year Tax</th><th>Survey Usage</th><th>MIS Usage</th></tr>
                                        </thead>
                                        <tbody>
                                            ${result.assessments.map(a => `
                                                <tr>
                                                    <td>${a.assessment}</td>
                                                    <td>${a.mis_owner_name || 'N/A'}</td>
                                                    <td>${formatNumber(a.mis_plot_area || 0)}</td>
                                                    <td>${formatNumber(a.mis_half_year_tax || 0)}</td>
                                                    <td class="${(a.bill_usage || '').toLowerCase().trim() !== (a.mis_usage || '').toLowerCase().trim() ? 'table-warning' : ''}">
                                                        ${capitalize(a.bill_usage || 'N/A')}
                                                    </td>
                                                    <td class="${(a.bill_usage || '').toLowerCase().trim() !== (a.mis_usage || '').toLowerCase().trim() ? 'table-warning' : ''}">
                                                        ${capitalize(a.mis_usage || 'N/A')}
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    const existingModal = document.getElementById('detailsModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add new modal
    document.getElementById('modalsContainer').innerHTML = modalHtml;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    modal.show();
}

function exportToExcel() {
    const filtered = getFilteredData();
    const exportData = filtered.map(item => ({
        'GIS ID': item.gisid,
        'Building Name': item.building_name,
        'Road Name': item.road_name,
        'Building Usage': item.building_usage,
        'Number Floor': item.number_floor,
        'Basement': item.basement,
        'Percentage': item.percentage,
        'Drone Area (sq.ft)': item.drone_area,
        'MIS Area (sq.ft)': item.mis_total_area,
        'Difference': item.area_difference,
        'Area Status': item.area_variation,
        'Usage Status': item.usage_variation ? 'MISMATCH' : 'MATCHED',
        'Assessment Count': item.assessment_count
    }));

    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Ward_Variations');
    XLSX.writeFile(wb, `ward_${currentWardNo}_variations.xlsx`);
}

const currentWardNo = '{{ $ward_no }}';

// Initialize table
renderTable();
</script>
@endsection

@endsection
