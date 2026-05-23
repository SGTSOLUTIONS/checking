{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward ' . $warddetail->ward_no . ' - Area Variations')

@section('content')

<div class="content-panel">
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
                    {{ $warddetail->corporation_id ?? 'N/A' }}
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

    {{-- SUMMARY CARDS --}}
    @php
        $totalDroneArea = collect($result)->sum('calculated_area');
        $totalMisArea = collect($result)->sum('mis_plot_area');
        $totalDifference = $totalDroneArea - $totalMisArea;

        $excessCount = collect($result)->where('area_variation', '>', 100)->count();
        $shortCount = collect($result)->where('area_variation', '<', -100)->count();
        $matchedCount = collect($result)->filter(function ($item) {
            return $item['area_variation'] >= -100 && $item['area_variation'] <= 100;
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
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Calculated Area</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalDroneArea, 2) }}</h3>
                        <small>sq.ft</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total MIS Area</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalMisArea, 2) }}</h3>
                        <small>sq.ft</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Difference</h6>
                        <h3 class="mb-0 fw-bold {{ $totalDifference > 0 ? 'text-danger' : ($totalDifference < 0 ? 'text-success' : 'text-secondary') }}">
                            {{ number_format(abs($totalDifference), 2) }}
                        </h3>
                        <small>
                            <span class="badge {{ $totalDifference > 0 ? 'bg-danger' : ($totalDifference < 0 ? 'bg-success' : 'bg-secondary') }}">
                                {{ $totalDifference > 0 ? 'EXCESS' : ($totalDifference < 0 ? 'SHORT' : 'MATCHED') }}
                            </span>
                        </small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Variation Range</h6>
                        <div class="text-success fw-bold">+{{ number_format($positiveVariation, 2) }}</div>
                        <div class="text-danger fw-bold">-{{ number_format(abs($negativeVariation), 2) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATUS SUMMARY BADGES --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <span class="badge bg-danger p-3 fs-6">
                                <i class="fas fa-arrow-up"></i> EXCESS
                            </span>
                            <h4 class="mt-2 mb-0">{{ $excessCount }}</h4>
                            <small>Buildings</small>
                        </div>
                        <div>
                            <span class="badge bg-info p-3 fs-6">
                                <i class="fas fa-equals"></i> MATCHED
                            </span>
                            <h4 class="mt-2 mb-0">{{ $matchedCount }}</h4>
                            <small>Buildings</small>
                        </div>
                        <div>
                            <span class="badge bg-success p-3 fs-6">
                                <i class="fas fa-arrow-down"></i> SHORT
                            </span>
                            <h4 class="mt-2 mb-0">{{ $shortCount }}</h4>
                            <small>Buildings</small>
                        </div>
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
                    <label class="form-label fw-bold">Area Status</label>
                    <select id="areaFilter" class="form-select">
                        <option value="all">All Buildings</option>
                        <option value="excess">Excess (+100 sq.ft)</option>
                        <option value="short">Short (-100 sq.ft)</option>
                        <option value="matched">Matched (±100 sq.ft)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Sort By</label>
                    <select id="sortBy" class="form-select">
                        <option value="gisid">GIS ID (A-Z)</option>
                        <option value="variation_high">Difference (High to Low)</option>
                        <option value="variation_low">Difference (Low to High)</option>
                        <option value="percentage_high">Variation % (High to Low)</option>
                        <option value="percentage_low">Variation % (Low to High)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Search GIS ID</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID...">
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-chart-pie me-2" style="color: #D4A13E;"></i>
                Variation Distribution
            </h6>
        </div>
        <div class="card-body">
            <canvas id="variationChart" height="80"></canvas>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-building me-2" style="color: #D4A13E;"></i>
                Detailed Building Analysis
            </h6>
            <div>
                <select id="perPage" class="form-select form-select-sm w-auto">
                    <option value="10">10 per page</option>
                    <option value="25" selected>25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                    <option value="all">Show All</option>
                </select>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="variationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>GIS ID</th>
                            <th>Polygon Area</th>
                            <th>Floors</th>
                            <th>Coverage %</th>
                            <th>Basement</th>
                            <th>Calculated Area</th>
                            <th>MIS Area</th>
                            <th>Difference</th>
                            <th>Variation %</th>
                            <th>Status</th>
                            <th>Assessments</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>

            <div class="row mt-3 p-3">
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

    // Filter by area status
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

    // Search by GIS ID
    if (currentSearch) {
        filtered = filtered.filter(item =>
            item.gisid.toString().toLowerCase().includes(currentSearch.toLowerCase())
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
            default: // gisid
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
    const itemsPerPage = displayAll ? filtered.length : parseInt(perPage);
    const totalItems = filtered.length;
    const totalPages = displayAll ? 1 : Math.ceil(totalItems / itemsPerPage);

    if (currentPage > totalPages) {
        currentPage = 1;
    }

    const start = (currentPage - 1) * itemsPerPage;
    const pageData = filtered.slice(start, start + itemsPerPage);

    if (pageData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-5">
                    <i class="fas fa-search fa-2x text-muted mb-2 d-block"></i>
                    No records found
                </td>
            </tr>
        `;
        renderPagination(totalPages, totalItems, itemsPerPage);
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
                <td class="fw-bold">${start + index + 1}</td>
                <td>
                    <strong class="text-primary">${result.gisid}</strong>
                </td>
                <td>${formatNumber(result.sqfeet)}</td>
                <td>${result.number_floor}</td>
                <td>${result.percentage}%</td>
                <td>${result.basement > 0 ? result.basement : '-'}</td>
                <td class="fw-bold">${formatNumber(result.calculated_area)}</td>
                <td>${formatNumber(result.mis_plot_area)}</td>
                <td class="${result.area_variation > 0 ? 'text-danger fw-bold' : 'text-success fw-bold'}">
                    ${result.area_variation > 0 ? '+' : ''}${formatNumber(result.area_variation)}
                </td>
                <td class="${result.variation_percentage > 0 ? 'text-danger' : 'text-success'}">
                    ${result.variation_percentage > 0 ? '+' : ''}${formatNumber(result.variation_percentage)}%
                </td>
                <td>${badge}</td>
                <td>
                    <span class="badge bg-secondary">${result.assessment_count}</span>
                    <button class="btn btn-sm btn-outline-primary ms-1" onclick="showDetails('${result.gisid}')">
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

    const start = totalItems === 0 ? 0 : ((currentPage - 1) * itemsPerPage) + 1;
    const end = Math.min(currentPage * itemsPerPage, totalItems);

    paginationInfo.innerHTML = `
        <div class="text-muted">
            <i class="fas fa-chart-simple me-1"></i>
            Showing ${start} to ${end} of ${totalItems} entries
        </div>
    `;

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">Previous</a>
    </li>`;

    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
        </li>`;
    }

    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">Next</a>
    </li>`;

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

    Swal.fire({
        title: `GIS ID: ${result.gisid}`,
        html: `
            <div class="text-start">
                <p><strong>Calculated Area:</strong> ${formatNumber(result.calculated_area)} sq.ft</p>
                <p><strong>MIS Area:</strong> ${formatNumber(result.mis_plot_area)} sq.ft</p>
                <p><strong>Difference:</strong> ${formatNumber(result.area_variation)} sq.ft</p>
                <p><strong>Variation:</strong> ${formatNumber(result.variation_percentage)}%</p>
                <p><strong>Polygon Area:</strong> ${formatNumber(result.sqfeet)} sq.ft</p>
                <p><strong>Floors:</strong> ${result.number_floor}</p>
                <p><strong>Coverage:</strong> ${result.percentage}%</p>
                <p><strong>Assessments:</strong> ${result.assessment_count}</p>
            </div>
        `,
        icon: 'info',
        confirmButtonColor: '#1A6B6E'
    });
}

function exportToExcel() {
    const filtered = getFilteredData();

    const exportData = filtered.map(item => ({
        'GIS ID': item.gisid,
        'Polygon Area (sq.ft)': item.sqfeet,
        'Number of Floors': item.number_floor,
        'Coverage Percentage': item.percentage,
        'Basement Area (sq.ft)': item.basement,
        'Calculated Area (sq.ft)': item.calculated_area,
        'MIS Area (sq.ft)': item.mis_plot_area,
        'Difference (sq.ft)': item.area_variation,
        'Variation Percentage': item.variation_percentage,
        'Assessment Count': item.assessment_count,
        'Status': item.area_variation > 100 ? 'EXCESS' : (item.area_variation < -100 ? 'SHORT' : 'MATCHED')
    }));

    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Ward_Variations');
    XLSX.writeFile(wb, `ward_${currentWardNo}_variations_${new Date().toISOString().split('T')[0]}.xlsx`);
}

function initChart() {
    const ctx = document.getElementById('variationChart');
    if (!ctx) return;

    const ranges = {
        'Excess (>100 sq.ft)': 0,
        'Matched (±100 sq.ft)': 0,
        'Short (<-100 sq.ft)': 0
    };

    resultsData.forEach(item => {
        if (item.area_variation > 100) {
            ranges['Excess (>100 sq.ft)']++;
        } else if (item.area_variation < -100) {
            ranges['Short (<-100 sq.ft)']++;
        } else {
            ranges['Matched (±100 sq.ft)']++;
        }
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(ranges),
            datasets: [{
                label: 'Number of Buildings',
                data: Object.values(ranges),
                backgroundColor: ['#dc3545', '#0dcaf0', '#198754'],
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Buildings: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Buildings',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Variation Category',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
}

// Event Listeners
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    renderTable();
});
</script>

{{-- SweetAlert for better modal --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
