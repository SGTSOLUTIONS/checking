{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward ' . $warddetail->ward_no . ' - Area Variations')

@section('content')

<div class="dashboard-content-area">
    <div class="animate__animated animate__fadeInUp">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <h3 class="fw-bold text-white mb-1">
                    <i class="fas fa-chart-bar me-2"></i>
                    Area Variation Analysis - Ward {{ $warddetail->ward_no }}
                </h3>
                <p class="text-white-50 mb-0">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ ucfirst($warddetail->zone) }} Zone | Corporation ID: {{ $warddetail->corporation_id ?? 'N/A' }}
                </p>
            </div>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-building me-1"></i>
                    Total Buildings: {{ count($result) }}
                </span>
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

        {{-- Statistics Cards - Row 1 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Calculated Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalDroneArea, 2) }}</h2>
                        <small class="text-info"><i class="fas fa-ruler-combined"></i> Square Feet</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-ruler-combined"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total MIS Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalMisArea, 2) }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> Square Feet</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Difference</h6>
                        <h2 class="fw-bold mb-0 {{ $totalDifference > 0 ? 'text-danger' : ($totalDifference < 0 ? 'text-success' : 'text-secondary') }}">
                            {{ number_format(abs($totalDifference), 2) }}
                        </h2>
                        <small>
                            <span class="badge {{ $totalDifference > 0 ? 'bg-danger' : ($totalDifference < 0 ? 'bg-success' : 'bg-secondary') }}">
                                {{ $totalDifference > 0 ? 'EXCESS' : ($totalDifference < 0 ? 'SHORT' : 'MATCHED') }}
                            </span>
                        </small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Variation Range</h6>
                        <div class="text-success fw-bold fs-4">+{{ number_format($positiveVariation, 2) }}</div>
                        <div class="text-danger fw-bold">-{{ number_format(abs($negativeVariation), 2) }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
                </div>
            </div>
        </div>

        {{-- Status Summary Badges --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card p-3 text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-icon bg-danger-subtle"><i class="fas fa-arrow-up text-danger"></i></div>
                        <div>
                            <h3 class="fw-bold mb-0 text-danger">{{ $excessCount }}</h3>
                            <small class="text-muted">EXCESS Buildings</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle invisible"><i class="fas fa-arrow-up"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-3 text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-icon bg-info-subtle"><i class="fas fa-equals text-info"></i></div>
                        <div>
                            <h3 class="fw-bold mb-0 text-info">{{ $matchedCount }}</h3>
                            <small class="text-muted">MATCHED Buildings</small>
                        </div>
                        <div class="stat-icon bg-info-subtle invisible"><i class="fas fa-equals"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-3 text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-icon bg-success-subtle"><i class="fas fa-arrow-down text-success"></i></div>
                        <div>
                            <h3 class="fw-bold mb-0 text-success">{{ $shortCount }}</h3>
                            <small class="text-muted">SHORT Buildings</small>
                        </div>
                        <div class="stat-icon bg-success-subtle invisible"><i class="fas fa-arrow-down"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Card --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>
                        Variation Distribution
                    </h5>
                    <canvas id="variationChart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-filter me-2" style="color:#1679AB;"></i>
                        Filter & Sort Data
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="text-dark fw-bold mb-2">Area Status</label>
                            <select id="areaFilter" class="form-select">
                                <option value="all">All Buildings</option>
                                <option value="excess">Excess (+100 sq.ft)</option>
                                <option value="short">Short (-100 sq.ft)</option>
                                <option value="matched">Matched (±100 sq.ft)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="text-dark fw-bold mb-2">Sort By</label>
                            <select id="sortBy" class="form-select">
                                <option value="gisid">GIS ID (A-Z)</option>
                                <option value="variation_high">Difference (High to Low)</option>
                                <option value="variation_low">Difference (Low to High)</option>
                                <option value="percentage_high">Variation % (High to Low)</option>
                                <option value="percentage_low">Variation % (Low to High)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="text-dark fw-bold mb-2">Search GIS ID</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID...">
                        </div>

                        <div class="col-md-2">
                            <label class="text-dark fw-bold mb-2">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="resetFilters()">
                                <i class="fas fa-sync-alt"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Table Card --}}
        <div class="row g-4">
            <div class="col-12">
                <div class="stat-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-building me-2" style="color:#1679AB;"></i>
                            Detailed Building Analysis
                        </h5>
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

                    {{-- Results Counter --}}
                    <div class="mb-3">
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-chart-bar me-1"></i>
                            Showing <span id="resultCount">0</span> buildings
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="variationsTable">
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

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                        <div class="mb-2 mb-sm-0" id="paginationInfo"></div>
                        <ul class="pagination justify-content-end" id="pagination"></ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    // Update result count
    document.getElementById('resultCount').innerText = totalItems;

    if (pageData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 d-block text-muted"></i>
                    <h5>No records found</h5>
                    <p class="mb-0 text-muted">Try adjusting your filters</p>
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
            badge = '<span class="badge bg-danger">EXCESS</span>';
        } else if (result.area_variation < -100) {
            badge = '<span class="badge bg-success">SHORT</span>';
        } else {
            badge = '<span class="badge bg-info">MATCHED</span>';
        }

        tbody.innerHTML += `
            <tr>
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
        <small class="text-muted">
            Showing ${start} to ${end} of ${totalItems} entries
        </small>
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
                <table class="table table-sm table-borderless">
                    <tr><td><strong>Calculated Area:</strong></td><td>${formatNumber(result.calculated_area)} sq.ft</td></tr>
                    <tr><td><strong>MIS Area:</strong></td><td>${formatNumber(result.mis_plot_area)} sq.ft</td></tr>
                    <tr><td><strong>Difference:</strong></td><td class="${result.area_variation > 0 ? 'text-danger' : 'text-success'}">${result.area_variation > 0 ? '+' : ''}${formatNumber(result.area_variation)} sq.ft</td></tr>
                    <tr><td><strong>Variation:</strong></td><td class="${result.variation_percentage > 0 ? 'text-danger' : 'text-success'}">${result.variation_percentage > 0 ? '+' : ''}${formatNumber(result.variation_percentage)}%</td></tr>
                    <tr><td><strong>Polygon Area:</strong></td><td>${formatNumber(result.sqfeet)} sq.ft</td></tr>
                    <tr><td><strong>Floors:</strong></td><td>${result.number_floor}</td></tr>
                    <tr><td><strong>Coverage:</strong></td><td>${result.percentage}%</td></tr>
                    <tr><td><strong>Assessments:</strong></td><td>${result.assessment_count}</td></tr>
                </table>
            </div>
        `,
        icon: 'info',
        confirmButtonColor: '#1679AB',
        confirmButtonText: 'Close'
    });
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
@endpush
