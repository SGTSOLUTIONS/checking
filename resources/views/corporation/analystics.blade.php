{{-- resources/views/corporation/analystics.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Analystics - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

@section('content')

<div class="dashboard-content-area">

    <div class="animate__animated animate__fadeInUp">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold text-white">
                <i class="fas fa-tachometer-alt me-2" style="color:#1679AB;"></i>
                Analystics Overview - {{ $corporation->name ?? '' }}
            </h3>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>

        <!-- Statistics Cards - Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_building) }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Polygon records</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Surveyed Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_surveyed_building) }}</h2>
                        <small class="text-info"><i class="fas fa-check-circle"></i> {{ $survey_percentage }}% coverage</small>
                    </div>
                    <div class="stat-icon bg-info-subtle"><i class="fas fa-clipboard-list text-info"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Surveyed Assessments</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_surveyed_assessment) }}</h2>
                        <small class="text-warning"><i class="fas fa-file-alt"></i> Point data records</small>
                    </div>
                    <div class="stat-icon bg-warning-subtle"><i class="fas fa-chart-line text-warning"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Records</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_mis) }}</h2>
                        <small class="text-danger"><i class="fas fa-database"></i> Total entries</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-database text-danger"></i></div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Row 2 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $ward_count }}</h2>
                        <small class="text-success"><i class="fas fa-map-marked-alt"></i> Active wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Shops</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shops) }}</h2>
                        <small class="text-primary"><i class="fas fa-store"></i> Registered shops</small>
                    </div>
                    <div class="stat-icon bg-primary-subtle"><i class="fas fa-store text-primary"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Shop Data in MIS</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shop_data_in_mis) }}</h2>
                        <small class="text-success"><i class="fas fa-link"></i> Matched records</small>
                    </div>
                    <div class="stat-icon bg-success-subtle"><i class="fas fa-check-double text-success"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Shop Data Not in MIS</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shop_data_not_in_mis) }}</h2>
                        <small class="text-danger"><i class="fas fa-unlink"></i> Unmatched records</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i>Ward-wise Building Statistics</h5>
                    <div class="chart-container">
                        <canvas id="buildingChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>Survey Coverage Overview</h5>
                    <div class="chart-container">
                        <canvas id="coveragePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-store me-2" style="color:#1679AB;"></i>Shop Data Matching Status</h5>
                    <div class="chart-container">
                        <canvas id="shopChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-simple me-2" style="color:#1679AB;"></i>Assessment Progress</h5>
                    <div class="chart-container">
                        <canvas id="assessmentDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ward-wise Statistics Table with Pagination -->
        <div class="row">
            <div class="col-12">
                <div class="stat-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                            Ward-wise Detailed Statistics
                        </h5>
                        <div class="mt-2 mt-sm-0">
                            <span class="badge bg-info">
                                <i class="fas fa-layer-group"></i> Showing {{ $wards_pagination->firstItem() }} to {{ $wards_pagination->lastItem() }} of {{ $wards_pagination->total() }} wards
                            </span>
                        </div>
                    </div>

                    <!-- Search Box -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="wardSearch" class="form-control"
                                       placeholder="Search by ward number or zone..."
                                       value="{{ request()->get('search') }}">
                                @if(request()->get('search'))
                                    <a href="{{ route('corporation.analystics') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="wardsTable">
                            <thead>
                                <tr>
                                    <th class="sortable" data-sort="zone">
                                        Zone
                                        @if(request()->get('sort') == 'zone')
                                            <i class="fas fa-sort-{{ request()->get('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th class="sortable" data-sort="ward_no">
                                        Ward No
                                        @if(request()->get('sort') == 'ward_no')
                                            <i class="fas fa-sort-{{ request()->get('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th class="sortable" data-sort="total_buildings">
                                        Total Buildings
                                        @if(request()->get('sort') == 'total_buildings')
                                            <i class="fas fa-sort-{{ request()->get('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Surveyed Buildings</th>
                                    <th>Surveyed Assessments</th>
                                    <th>Shops</th>
                                    <th>Shop Data</th>
                                    <th>In MIS</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wards as $ward)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ ucfirst($ward['zone']) }}</span></td>
                                    <td><strong>{{ $ward['ward_no'] }}</strong></td>
                                    <td>{{ number_format($ward['total_buildings']) }}</td>
                                    <td>
                                        {{ number_format($ward['surveyed_buildings']) }}
                                        @php
                                            $wardPct = $ward['total_buildings'] > 0
                                                ? round(($ward['surveyed_buildings'] / $ward['total_buildings']) * 100)
                                                : 0;
                                        @endphp
                                        <small class="text-muted">({{ $wardPct }}%)</small>
                                    </td>
                                    <td>{{ number_format($ward['surveyed_assessment']) }}</td>
                                    <td>{{ number_format($ward['shop_count']) }}</td>
                                    <td>{{ number_format($ward['shop_data_count']) }}</td>
                                    <td>
                                        @if($ward['shop_data_in_mis_count'] > 0)
                                            <span class="badge bg-success">{{ number_format($ward['shop_data_in_mis_count']) }}</span>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('corporation.ward.map', $ward['ward_no']) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-map-marked-alt"></i> Map
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No ward data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination and Per Page Controls -->
                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                        <div class="mb-2 mb-sm-0">
                            <small class="text-muted">
                                Showing {{ $wards_pagination->firstItem() ?? 0 }} to {{ $wards_pagination->lastItem() ?? 0 }} of {{ $wards_pagination->total() ?? 0 }} entries
                            </small>
                        </div>
                        <div>
                            {{ $wards_pagination->appends(request()->query())->links() }}
                        </div>
                    </div>

                    <!-- Per Page Selector -->
                    <div class="mt-3 d-flex align-items-center">
                        <label class="text-muted me-2 mb-0">Show per page:</label>
                        <select id="perPageSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="15" {{ request()->get('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request()->get('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request()->get('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request()->get('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>

                        <!-- Export Buttons -->
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-success me-2" id="exportExcelBtn">
                                <i class="fas fa-file-excel"></i> Export
                            </button>
                            <button class="btn btn-sm btn-outline-danger" id="exportPdfBtn">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Get data from Laravel
    const wardsData = @json($wards);
    const totalBuildings = {{ $total_building }};
    const totalSurveyedBuildings = {{ $total_surveyed_building }};
    const totalSurveyedAssessment = {{ $total_surveyed_assessment }};
    const totalShopDataInMis = {{ $total_shop_data_in_mis }};
    const totalShopDataNotInMis = {{ $total_shop_data_not_in_mis }};
    const total_mis = {{ $total_mis }};

    if (!wardsData || wardsData.length === 0) {
        // Show message if no data for charts
        const chartContainers = document.querySelectorAll('.chart-container');
        if (chartContainers.length > 0 && totalBuildings === 0) {
            chartContainers.forEach(container => {
                if (container.querySelector('canvas')) {
                    container.innerHTML = `<div class="alert alert-info text-center m-0">No data available for charts</div>`;
                }
            });
        }
        return;
    }

    // Prepare data for charts
    const labels = wardsData.map(w => w.ward_no);
    const buildingsData = wardsData.map(w => w.total_buildings);
    const surveyedData = wardsData.map(w => w.surveyed_buildings);
    const assessmentsData = wardsData.map(w => w.surveyed_assessment);

    // ========== BAR CHART: Buildings vs Surveyed ==========
    const barCtx = document.getElementById('buildingChart');
    if (barCtx) {
        new Chart(barCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Buildings',
                        data: buildingsData,
                        backgroundColor: '#102C57',
                        borderRadius: 6
                    },
                    {
                        label: 'Surveyed Buildings',
                        data: surveyedData,
                        backgroundColor: '#1679AB',
                        borderRadius: 6
                    },
                    {
                        label: 'Surveyed Assessments',
                        data: assessmentsData,
                        backgroundColor: '#FFC107',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ========== PIE CHART: Survey Coverage ==========
    const notSurveyed = totalBuildings - totalSurveyedBuildings;
    const pieCtx = document.getElementById('coveragePieChart');
    if (pieCtx && totalBuildings > 0) {
        new Chart(pieCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Surveyed Buildings', 'Not Surveyed'],
                datasets: [{
                    data: [totalSurveyedBuildings, notSurveyed],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = totalBuildings;
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ========== HORIZONTAL BAR: Shop Data Status ==========
    const shopCtx = document.getElementById('shopChart');
    if (shopCtx) {
        new Chart(shopCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Shop Data in MIS', 'Shop Data Not in MIS'],
                datasets: [{
                    label: 'Count',
                    data: [totalShopDataInMis, totalShopDataNotInMis],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Records'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ========== DOUGHNUT CHART: Assessment Progress ==========
    const remainingAssessment = total_mis > totalSurveyedAssessment
        ? total_mis - totalSurveyedAssessment
        : 0;
    const doughnutCtx = document.getElementById('assessmentDoughnutChart');
    if (doughnutCtx && total_mis > 0) {
        new Chart(doughnutCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Surveyed Assessments', 'Pending Assessments'],
                datasets: [{
                    data: [totalSurveyedAssessment, remainingAssessment],
                    backgroundColor: ['#17a2b8', '#6c757d'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = total_mis;
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ========== PAGINATION AND FILTER CONTROLS ==========

    // Per page selector functionality
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        });
    }

    // Search functionality
    const searchInput = document.getElementById('wardSearch');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                if (this.value.trim()) {
                    url.searchParams.set('search', this.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            }, 500);
        });
    }

    // Sort functionality
    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const sortField = this.getAttribute('data-sort');
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentDirection = url.searchParams.get('direction');

            let newDirection = 'asc';
            if (currentSort === sortField && currentDirection === 'asc') {
                newDirection = 'desc';
            }

            url.searchParams.set('sort', sortField);
            url.searchParams.set('direction', newDirection);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        });
    });

    // Export to Excel functionality
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('export', 'excel');
            window.location.href = url.toString();
        });
    }

    // Export to PDF functionality
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('export', 'pdf');
            window.location.href = url.toString();
        });
    }

});
</script>

<style>
.dashboard-content-area {
    padding: 20px;
    background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
    min-height: 100vh;
}

.stat-card {
    background: rgba(255,255,255,0.96);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: none;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 55px;
    height: 55px;
    background: rgba(22,121,171,0.1);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #1679AB;
}

.chart-container {
    position: relative;
    width: 100%;
    height: 320px;
}

.table th {
    background: #102C57;
    color: #fff;
    border: none;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.badge {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 20px;
}

.btn-sm {
    border-radius: 8px;
    padding: 5px 12px;
}

h3, h5 {
    letter-spacing: -0.3px;
}

/* Pagination Styling */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #102C57;
    border-radius: 8px;
    margin: 0 2px;
}

.page-item.active .page-link {
    background-color: #1679AB;
    border-color: #1679AB;
    color: white;
}

.page-link:hover {
    color: #1679AB;
}

/* Sortable headers */
.sortable {
    position: relative;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(255,255,255,0.1);
}

/* Responsive Design */
@media(max-width:768px){
    .dashboard-content-area {
        padding: 15px;
    }
    .chart-container {
        height: 250px;
    }
    .stat-card {
        margin-bottom: 15px;
    }
    .table-responsive {
        font-size: 12px;
    }
    .btn-sm {
        padding: 3px 8px;
        font-size: 11px;
    }
    .badge {
        padding: 4px 8px;
        font-size: 10px;
    }
}

/* Loading animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate__animated {
    animation-duration: 0.6s;
}

.animate__fadeInUp {
    animation-name: fadeIn;
}

/* Custom scrollbar */
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
</style>
@endpush
