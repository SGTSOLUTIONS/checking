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
                Dashboard Overview
            </h3>

            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
            </div>

        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">

            <!-- Total Buildings -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_building ?? 0) }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Polygon records</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>

            <!-- Surveyed Buildings -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Surveyed Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_surveyed_building ?? 0) }}</h2>
                        <small class="text-info"><i class="fas fa-check-circle"></i> {{ $survey_percentage ?? 0 }}% coverage</small>
                    </div>
                    <div class="stat-icon bg-info-subtle"><i class="fas fa-clipboard-list text-info"></i></div>
                </div>
            </div>

            <!-- Surveyed Assessments -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Surveyed Assessments</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_surveyed_assessment ?? 0) }}</h2>
                        <small class="text-warning"><i class="fas fa-file-alt"></i> Point data records</small>
                    </div>
                    <div class="stat-icon bg-warning-subtle"><i class="fas fa-chart-line text-warning"></i></div>
                </div>
            </div>

            <!-- MIS Records -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Records</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_mis ?? 0) }}</h2>
                        <small class="text-danger"><i class="fas fa-database"></i> Total entries</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-database text-danger"></i></div>
                </div>
            </div>

        </div>

        <!-- Second Row Stats -->
        <div class="row g-4 mb-4">

            <!-- Total Wards -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $wards->count() ?? 0 }}</h2>
                        <small class="text-success"><i class="fas fa-map-marked-alt"></i> Active wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                </div>
            </div>

            <!-- Total Shops -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Shops</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shops ?? 0) }}</h2>
                        <small class="text-primary"><i class="fas fa-store"></i> Registered shops</small>
                    </div>
                    <div class="stat-icon bg-primary-subtle"><i class="fas fa-store text-primary"></i></div>
                </div>
            </div>

            <!-- Shop Data in MIS -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Shop Data in MIS</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shop_data_in_mis ?? 0) }}</h2>
                        <small class="text-success"><i class="fas fa-link"></i> Matched records</small>
                    </div>
                    <div class="stat-icon bg-success-subtle"><i class="fas fa-check-double text-success"></i></div>
                </div>
            </div>

            <!-- Shop Data Not in MIS -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Shop Data Not in MIS</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shop_data_not_in_mis ?? 0) }}</h2>
                        <small class="text-danger"><i class="fas fa-unlink"></i> Unmatched records</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                </div>
            </div>

        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">

            <!-- Bar Chart: Buildings vs Surveyed -->
            <div class="col-lg-7">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i>Ward-wise Building Statistics</h5>
                    <div class="chart-container"><canvas id="buildingChart"></canvas></div>
                </div>
            </div>

            <!-- Pie Chart: Survey Coverage -->
            <div class="col-lg-5">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>Survey Coverage Overview</h5>
                    <div class="chart-container"><canvas id="coveragePieChart"></canvas></div>
                </div>
            </div>

        </div>

        <!-- Second Charts Row -->
        <div class="row g-4 mb-4">

            <!-- Horizontal Bar: Shop Data Status -->
            <div class="col-lg-6">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-store me-2" style="color:#1679AB;"></i>Shop Data Matching Status</h5>
                    <div class="chart-container"><canvas id="shopChart"></canvas></div>
                </div>
            </div>

            <!-- Doughnut: Assessment Progress -->
            <div class="col-lg-6">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-simple me-2" style="color:#1679AB;"></i>Assessment Progress</h5>
                    <div class="chart-container"><canvas id="assessmentDoughnutChart"></canvas></div>
                </div>
            </div>

        </div>

        <!-- Ward-wise Statistics Table -->
        <div class="row">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-table me-2" style="color:#1679AB;"></i>Ward-wise Detailed Statistics</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="wardsTable">
                            <thead>
                                <tr><th>Zone</th><th>Ward No</th><th>Total Buildings</th><th>Surveyed Buildings</th><th>Surveyed Assessments</th><th>Shops</th><th>Shop Data</th><th>In MIS</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse($wards as $ward)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ ucfirst($ward['zone']) }}</span></td>
                                    <td><strong>{{ $ward['ward_no'] }}</strong></td>
                                    <td>{{ number_format($ward['total_buildings']) }}</td>
                                    <td>
                                        {{ number_format($ward['surveyed_buildings']) }}
                                        @php $wardPct = $ward['total_buildings'] > 0 ? round(($ward['surveyed_buildings'] / $ward['total_buildings']) * 100) : 0; @endphp
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
                                        <a href="{{ route('corporation.ward.map', $ward['ward_no']) }}" class="btn btn-primary btn-sm"><i class="fas fa-map-marked-alt"></i> Map</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center text-muted">No ward data available</td></tr>
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
    const totalBuildings = {{ $total_building ?? 0 }};
    const totalSurveyedBuildings = {{ $total_surveyed_building ?? 0 }};
    const totalSurveyedAssessment = {{ $total_surveyed_assessment ?? 0 }};
    const totalMis = {{ $total_mis ?? 0 }};
    const totalShops = {{ $total_shops ?? 0 }};
    const totalShopDataInMis = {{ $total_shop_data_in_mis ?? 0 }};
    const totalShopDataNotInMis = {{ $total_shop_data_not_in_mis ?? 0 }};

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
    const shopsData = wardsData.map(w => w.shop_count);
    const shopDataInMisData = wardsData.map(w => w.shop_data_in_mis_count);
    const shopDataNotInMisData = wardsData.map(w => w.shop_data_not_in_mis_count);

    // ========== BAR CHART: Buildings vs Surveyed ==========
    const barCtx = document.getElementById('buildingChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Total Buildings', data: buildingsData, backgroundColor: '#102C57', borderRadius: 6 },
                { label: 'Surveyed Buildings', data: surveyedData, backgroundColor: '#1679AB', borderRadius: 6 },
                { label: 'Surveyed Assessments', data: assessmentsData, backgroundColor: '#FFC107', borderRadius: 6 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Count' } } } }
    });

    // ========== PIE CHART: Survey Coverage ==========
    const notSurveyed = totalBuildings - totalSurveyedBuildings;
    const pieCtx = document.getElementById('coveragePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Surveyed Buildings', 'Not Surveyed'],
            datasets: [{ data: [totalSurveyedBuildings, notSurveyed], backgroundColor: ['#28a745', '#dc3545'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw.toLocaleString()} (${((ctx.raw / totalBuildings) * 100).toFixed(1)}%)` } } } }
    });

    // ========== HORIZONTAL BAR: Shop Data Status ==========
    const shopCtx = document.getElementById('shopChart').getContext('2d');
    new Chart(shopCtx, {
        type: 'bar',
        data: {
            labels: ['Shop Data in MIS', 'Shop Data Not in MIS'],
            datasets: [{ label: 'Count', data: [totalShopDataInMis, totalShopDataNotInMis], backgroundColor: ['#28a745', '#dc3545'], borderRadius: 6 }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { beginAtZero: true } } }
    });

    // ========== DOUGHNUT CHART: Assessment Progress ==========
    const remainingAssessment = totalBuildings > totalSurveyedAssessment ? totalBuildings - totalSurveyedAssessment : 0;
    const doughnutCtx = document.getElementById('assessmentDoughnutChart').getContext('2d');
    new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Surveyed Assessments', 'Pending Assessments'],
            datasets: [{ data: [totalSurveyedAssessment, remainingAssessment], backgroundColor: ['#17a2b8', '#6c757d'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw.toLocaleString()} (${((ctx.raw / totalBuildings) * 100).toFixed(1)}%)` } } } }
    });

});
</script>

<style>
.dashboard-content-area { padding: 20px; background: linear-gradient(135deg, #102C57 0%, #1679AB 100%); min-height: 100vh; }
.stat-card { background: rgba(255,255,255,0.96); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: all 0.3s ease; border: none; backdrop-filter: blur(2px); }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
.stat-icon { width: 55px; height: 55px; background: rgba(22,121,171,0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #1679AB; }
.chart-container { position: relative; width: 100%; height: 320px; }
.table th { background: #102C57; color: #fff; border: none; font-weight: 600; }
.table td { vertical-align: middle; }
.badge { padding: 6px 12px; font-size: 12px; font-weight: 500; border-radius: 20px; }
.btn-sm { border-radius: 8px; padding: 5px 12px; }
h3, h5 { letter-spacing: -0.3px; }
@media(max-width:768px){
    .dashboard-content-area { padding: 15px; }
    .chart-container { height: 250px; }
    .stat-card { margin-bottom: 15px; }
}
</style>
@endpush
