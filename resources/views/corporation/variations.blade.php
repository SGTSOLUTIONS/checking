@extends('layouts.commissioner')

@section('title', 'Ward ' . $warddetail->ward_no . ' - Area Variations')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Ward {{ $warddetail->ward_no }} - {{ ucfirst($warddetail->zone) }} Zone
                            <small class="text-white-50">Area Variation Analysis Report</small>
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
                            <strong>Total Buildings:</strong> {{ count($result) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    @php
        $totalDroneArea = collect($result)->sum('calculated_area');
        $totalMisArea = collect($result)->sum('mis_plot_area');
        $totalDifference = $totalDroneArea - $totalMisArea;

        $excessCount = collect($result)->filter(function($item) {
            return $item['area_variation'] > 100;
        })->count();

        $shortCount = collect($result)->filter(function($item) {
            return $item['area_variation'] < -100;
        })->count();

        $matchedCount = collect($result)->filter(function($item) {
            return $item['area_variation'] >= -100 && $item['area_variation'] <= 100;
        })->count();

        $positiveVariation = collect($result)->filter(function($item) {
            return $item['area_variation'] > 0;
        })->sum('area_variation');

        $negativeVariation = collect($result)->filter(function($item) {
            return $item['area_variation'] < 0;
        })->sum('area_variation');
    @endphp

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Calculated Area</h6>
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
            <div class="card {{ $totalDifference > 0 ? 'bg-danger' : ($totalDifference < 0 ? 'bg-success' : 'bg-secondary') }} text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Difference</h6>
                    <h3 class="mb-0">{{ number_format(abs($totalDifference), 2) }} sq.ft</h3>
                    <small>{{ $totalDifference > 0 ? 'EXCESS' : ($totalDifference < 0 ? 'SHORT' : 'MATCHED') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6 class="card-title">Variation Range</h6>
                    <h6 class="mb-0">+{{ number_format($positiveVariation, 2) }}</h6>
                    <h6 class="mb-0">-{{ number_format(abs($negativeVariation), 2) }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Variation Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Area Variation Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="bg-danger text-white p-3 rounded">
                                <h4 class="mb-0">{{ $excessCount }}</h4>
                                <small>EXCESS (>100)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-success text-white p-3 rounded">
                                <h4 class="mb-0">{{ $shortCount }}</h4>
                                <small>SHORT (<-100)</small>
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Variation Percentage Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="variationChart" height="80"></canvas>
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
                                <option value="excess">EXCESS (Diff > 100)</option>
                                <option value="short">SHORT (Diff < -100)</option>
                                <option value="matched">MATCHED (|Diff| ≤ 100)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Sort by:</label>
                            <select id="sortBy" class="form-select">
                                <option value="gisid">GIS ID</option>
                                <option value="variation_high">Variation (High to Low)</option>
                                <option value="variation_low">Variation (Low to High)</option>
                                <option value="percentage_high">Percentage (High to Low)</option>
                                <option value="percentage_low">Percentage (Low to High)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Search GIS ID:</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID...">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button onclick="resetFilters()" class="btn btn-secondary form-control">Reset Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results Table -->
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
                                    <option value="all">All</option>
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
                                    <th style="width: 15%">GIS ID</th>
                                    <th style="width: 8%">Polygon Area</th>
                                    <th style="width: 8%">Floors</th>
                                    <th style="width: 5%">%</th>
                                    <th style="width: 5%">B</th>
                                    <th style="width: 10%">Calculated Area</th>
                                    <th style="width: 10%">MIS Area</th>
                                    <th style="width: 10%">Difference</th>
                                    <th style="width: 8%">Variation %</th>
                                    <th style="width: 8%">Status</th>
                                    <th style="width: 8%">Assessments</th>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// Convert PHP data to JavaScript
const resultsData = @json($result);
let currentPage = 1;
let perPage = 25;
let currentAreaFilter = 'all';
let currentSortBy = 'gisid';
let currentSearch = '';

// Get filtered and sorted data
function getFilteredData() {
    let filtered = [...resultsData];

    // Area filter
    if (currentAreaFilter !== 'all') {
        filtered = filtered.filter(item => {
            if (currentAreaFilter === 'excess') return item.area_variation > 100;
            if (currentAreaFilter === 'short') return item.area_variation < -100;
            if (currentAreaFilter === 'matched') return Math.abs(item.area_variation) <= 100;
            return true;
        });
    }

    // Search filter
    if (currentSearch) {
        filtered = filtered.filter(item =>
            item.gisid.toString().toLowerCase().includes(currentSearch.toLowerCase())
        );
    }

    // Sorting
    filtered.sort((a, b) => {
        switch(currentSortBy) {
            case 'gisid':
                return a.gisid.localeCompare(b.gisid);
            case 'variation_high':
                return Math.abs(b.area_variation) - Math.abs(a.area_variation);
            case 'variation_low':
                return Math.abs(a.area_variation) - Math.abs(b.area_variation);
            case 'percentage_high':
                return b.variation_percentage - a.variation_percentage;
            case 'percentage_low':
                return a.variation_percentage - b.variation_percentage;
            default:
                return 0;
        }
    });

    return filtered;
}

// Render table
function renderTable() {
    const filtered = getFilteredData();
    const totalItems = filtered.length;
    const displayAll = perPage === 'all';
    const itemsPerPage = displayAll ? totalItems : parseInt(perPage);
    const totalPages = displayAll ? 1 : Math.ceil(totalItems / itemsPerPage);

    // Ensure current page is valid
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filtered.slice(start, end);

    // Render table body
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12" class="text-center">No records found</td></tr>';
        document.getElementById('paginationInfo').innerHTML = 'Showing 0 to 0 of 0 entries';
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    pageData.forEach((result, index) => {
        const row = tbody.insertRow();
        const serialNo = start + index + 1;

        let statusClass = '';
        let statusBadge = '';

        if (result.area_variation > 100) {
            statusClass = 'table-danger';
            statusBadge = '<span class="badge bg-danger">EXCESS</span>';
        } else if (result.area_variation < -100) {
            statusClass = 'table-success';
            statusBadge = '<span class="badge bg-success">SHORT</span>';
        } else {
            statusClass = 'table-info';
            statusBadge = '<span class="badge bg-info">MATCHED</span>';
        }

        row.className = statusClass;

        row.innerHTML = `
            <td>${serialNo}</td>
            <td><strong>${result.gisid}</strong></td>
            <td>${formatNumber(result.sqfeet)}</td>
            <td>${result.number_floor}</td>
            <td>${result.percentage}%</td>
            <td>${result.basement > 0 ? result.basement : '-'}</td>
            <td><strong>${formatNumber(result.calculated_area)}</strong></td>
            <td>${formatNumber(result.mis_plot_area)}</td>
            <td class="${result.area_variation > 0 ? 'text-danger' : (result.area_variation < 0 ? 'text-success' : '')}">
                <strong>${result.area_variation > 0 ? '+' : ''}${formatNumber(result.area_variation)}</strong>
            </td>
            <td>
                ${result.variation_percentage > 0 ? '+' : ''}${formatNumber(result.variation_percentage)}%
                ${Math.abs(result.variation_percentage) > 50 ? '⚠️' : ''}
            </td>
            <td>${statusBadge}</td>
            <td>
                <span class="badge bg-secondary">${result.assessment_count}</span>
                <button type="button" class="btn btn-sm btn-outline-info ms-1" onclick="showDetails(${serialNo - 1})" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
             </td>
        `;
    });

    // Render pagination
    renderPagination(totalPages, totalItems, itemsPerPage);
}

function renderPagination(totalPages, totalItems, itemsPerPage) {
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');

    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(start + itemsPerPage - 1, totalItems);

    paginationInfo.innerHTML = `Showing ${start} to ${end} of ${totalItems} entries`;

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
    </li>`;

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

    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
    </li>`;

    pagination.innerHTML = html;
}

function changePage(page) {
    const filtered = getFilteredData();
    const displayAll = perPage === 'all';
    const itemsPerPage = displayAll ? filtered.length : parseInt(perPage);
    const totalPages = displayAll ? 1 : Math.ceil(filtered.length / itemsPerPage);

    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable();
}

// Helper functions
function formatNumber(num) {
    if (num === undefined || num === null) return '0.00';
    return Number(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function capitalize(str) {
    if (!str) return 'N/A';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

// Show details modal
function showDetails(index) {
    const filtered = getFilteredData();
    const displayAll = perPage === 'all';
    const itemsPerPage = displayAll ? filtered.length : parseInt(perPage);
    const start = (currentPage - 1) * itemsPerPage;
    const result = filtered[start + index];

    if (!result) return;

    const modalHtml = `
        <div class="modal fade" id="detailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">GIS ID: ${result.gisid}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Polygon Area:</strong> ${formatNumber(result.sqfeet)} sq.ft<br>
                                <strong>Number of Floors:</strong> ${result.number_floor}<br>
                                <strong>Percentage:</strong> ${result.percentage}%<br>
                                <strong>Basement:</strong> ${result.basement || '0'}
                            </div>
                            <div class="col-md-6">
                                <strong>Calculated Area:</strong> ${formatNumber(result.calculated_area)} sq.ft<br>
                                <strong>MIS Plot Area:</strong> ${formatNumber(result.mis_plot_area)} sq.ft<br>
                                <strong>Area Difference:</strong> ${result.area_variation > 0 ? '+' : ''}${formatNumber(result.area_variation)} sq.ft<br>
                                <strong>Variation %:</strong> ${result.variation_percentage > 0 ? '+' : ''}${formatNumber(result.variation_percentage)}%
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <strong>Assessment Count:</strong> ${result.assessment_count}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    const existingModal = document.getElementById('detailsModal');
    if (existingModal) existingModal.remove();

    // Add new modal
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer.firstElementChild);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    modal.show();
}

// Export to Excel
function exportToExcel() {
    const filtered = getFilteredData();
    const exportData = filtered.map(item => ({
        'GIS ID': item.gisid,
        'Polygon Area (sq.ft)': item.sqfeet,
        'Number of Floors': item.number_floor,
        'Percentage (%)': item.percentage,
        'Basement': item.basement,
        'Calculated Area (sq.ft)': item.calculated_area,
        'MIS Plot Area (sq.ft)': item.mis_plot_area,
        'Area Difference (sq.ft)': item.area_variation,
        'Variation (%)': item.variation_percentage,
        'Status': item.area_variation > 100 ? 'EXCESS' : (item.area_variation < -100 ? 'SHORT' : 'MATCHED'),
        'Assessment Count': item.assessment_count
    }));

    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Ward_Variations');
    XLSX.writeFile(wb, `ward_${currentWardNo}_variations.xlsx`);
}

// Initialize chart
function initChart() {
    const ctx = document.getElementById('variationChart').getContext('2d');
    const ranges = {
        'High Excess (>500)': 0,
        'Excess (100-500)': 0,
        'Normal (-100 to 100)': 0,
        'Short (-500 to -100)': 0,
        'High Short (<-500)': 0
    };

    resultsData.forEach(item => {
        const diff = item.area_variation;
        if (diff > 500) ranges['High Excess (>500)']++;
        else if (diff > 100) ranges['Excess (100-500)']++;
        else if (diff >= -100) ranges['Normal (-100 to 100)']++;
        else if (diff >= -500) ranges['Short (-500 to -100)']++;
        else ranges['High Short (<-500)']++;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(ranges),
            datasets: [{
                label: 'Number of Buildings',
                data: Object.values(ranges),
                backgroundColor: ['#dc3545', '#fd7e14', '#0dcaf0', '#198754', '#6c757d'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: false }
            }
        }
    });
}

// Event listeners
document.getElementById('areaFilter').addEventListener('change', function(e) {
    currentAreaFilter = e.target.value;
    currentPage = 1;
    renderTable();
});

document.getElementById('sortBy').addEventListener('change', function(e) {
    currentSortBy = e.target.value;
    currentPage = 1;
    renderTable();
});

document.getElementById('searchInput').addEventListener('keyup', function(e) {
    currentSearch = e.target.value;
    currentPage = 1;
    renderTable();
});

document.getElementById('perPage').addEventListener('change', function(e) {
    perPage = e.target.value;
    currentPage = 1;
    renderTable();
});

function resetFilters() {
    document.getElementById('areaFilter').value = 'all';
    document.getElementById('sortBy').value = 'gisid';
    document.getElementById('searchInput').value = '';
    currentAreaFilter = 'all';
    currentSortBy = 'gisid';
    currentSearch = '';
    currentPage = 1;
    renderTable();
}

const currentWardNo = '{{ $warddetail->ward_no }}';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    renderTable();
});
</script>
@endsection

@endsection
