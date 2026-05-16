{{-- resources/views/corporation/dashboard.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Dashboard - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

@section('content')

<div class="dashboard-content-area">

    <div class="animate__animated animate__fadeInUp">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold text-white">
                <i class="fas fa-tachometer-alt me-2" style="color:#1679AB;"></i>
                Dashboard Overview - {{ $corporation->name ?? '' }}
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

        <!-- Ward-wise Statistics Table -->
        <div class="row">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                        Ward-wise Detailed Statistics
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="wardsTable">
                            <thead>
                                <tr>
                                    <th>Zone</th>
                                    <th>Ward No</th>
                                    <th>Total Buildings</th>
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
                                    <td colspan="9" class="text-center text-muted">No ward data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Get data from Laravel
    const wardsData = @json($wards);
    const totalBuildings = {{ $total_building }};
    const totalSurveyedBuildings = {{ $total_surveyed_building }};
    const totalSurveyedAssessment = {{ $total_surveyed_assessment }};
    const totalShopDataInMis = {{ $total_shop_data_in_mis }};
    const totalShopDataNotInMis = {{ $total_shop_data_not_in_mis }};
    const total_mis ={{$total_mis}};

    if (!wardsData || wardsData.length === 0) {
        document.querySelectorAll('.chart-container').forEach(container => {
            container.innerHTML = `<div class="alert alert-info text-center m-0">No data available for charts</div>`;
        });
        return;
    }

    // Prepare data for charts
    const labels = wardsData.map(w => w.ward_no);
    const buildingsData = wardsData.map(w => w.total_buildings);
    const surveyedData = wardsData.map(w => w.surveyed_buildings);
    const assessmentsData = wardsData.map(w => w.surveyed_assessment);

    // ========== BAR CHART: Buildings vs Surveyed ==========
    const barCtx = document.getElementById('buildingChart').getContext('2d');
    new Chart(barCtx, {
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
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // ========== PIE CHART: Survey Coverage ==========
    const notSurveyed = totalBuildings - totalSurveyedBuildings;
    const pieCtx = document.getElementById('coveragePieChart').getContext('2d');
    new Chart(pieCtx, {
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

    // ========== HORIZONTAL BAR: Shop Data Status ==========
    const shopCtx = document.getElementById('shopChart').getContext('2d');
    new Chart(shopCtx, {
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
                    }
                }
            }
        }
    });

    // ========== DOUGHNUT CHART: Assessment Progress ==========
    const remainingAssessment = total_mis > totalSurveyedAssessment
        ? total_mis - totalSurveyedAssessment
        : 0;
    const doughnutCtx = document.getElementById('assessmentDoughnutChart').getContext('2d');
    new Chart(doughnutCtx, {
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
}
</style>
@endpush
