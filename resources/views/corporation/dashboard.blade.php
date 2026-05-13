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

        <!-- Statistics -->
        <div class="row g-4 mb-4">

            <!-- Total Wards -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">

                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>

                        <h2 class="fw-bold mb-0" style="color:#102C57;">
                            {{ $ward_count ?? 0 }}
                        </h2>

                        <small class="text-success">
                            <i class="fas fa-map-marked-alt"></i>
                            Active wards
                        </small>
                    </div>

                    <div class="stat-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>

                </div>
            </div>

            <!-- Total Buildings -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">

                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>

                        <h2 class="fw-bold mb-0" style="color:#102C57;">
                            {{ $total_buildings ?? 0 }}
                        </h2>

                        <small class="text-success">
                            <i class="fas fa-building"></i>
                            Across all wards
                        </small>
                    </div>

                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>

                </div>
            </div>

            <!-- Area Variation -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">

                    <div>
                        <h6 class="text-muted mb-1">Area Variation</h6>

                        <h2 class="fw-bold mb-0" style="color:#102C57;">
                            {{ $total_area_variation ?? 0 }}
                        </h2>

                        <small class="text-warning">
                            <i class="fas fa-chart-line"></i>
                            {{ $area_variation_percentage ?? 0 }}%
                        </small>
                    </div>

                    <div class="stat-icon bg-warning-subtle">
                        <i class="fas fa-arrows-alt text-warning"></i>
                    </div>

                </div>
            </div>

            <!-- Usage Variation -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">

                    <div>
                        <h6 class="text-muted mb-1">Usage Variation</h6>

                        <h2 class="fw-bold mb-0" style="color:#102C57;">
                            {{ $total_usage_variation ?? 0 }}
                        </h2>

                        <small class="text-info">
                            <i class="fas fa-exchange-alt"></i>
                            {{ $usage_variation_percentage ?? 0 }}%
                        </small>
                    </div>

                    <div class="stat-icon bg-info-subtle">
                        <i class="fas fa-building text-info"></i>
                    </div>

                </div>
            </div>

        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">

            <!-- Bar Chart -->
            <div class="col-lg-7">

                <div class="stat-card p-3">

                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i>
                        Ward-wise Area & Usage Variation
                    </h5>

                    <div class="chart-container">
                        <canvas id="areaVariationChart"></canvas>
                    </div>

                </div>

            </div>

            <!-- Pie Chart -->
            <div class="col-lg-5">

                <div class="stat-card p-3">

                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>
                        Overall Variation Summary
                    </h5>

                    <div class="chart-container">
                        <canvas id="pieChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

        <!-- Table -->
        <div class="row">

            <div class="col-12">

                <div class="stat-card p-4">

                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                        Ward-wise Statistics
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle" id="wardsTable">

                            <thead>
                                <tr>
                                    <th>Zone</th>
                                    <th>Ward No</th>
                                    <th>Buildings</th>
                                    <th>Surveyed</th>
                                    <th>Area Variation</th>
                                    <th>Usage Variation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($collections as $data)

                                <tr>

                                    <td>{{ ucfirst($data['zone']) }}</td>

                                    <td>{{ $data['ward_no'] }}</td>

                                    <td>{{ $data['buildingCount'] ?? 0 }}</td>

                                    <td>{{ $data['surveyedBuildingCount'] ?? 0 }}</td>

                                    <td>
                                        <span class="badge {{ ($data['areaVariationCount'] ?? 0) > 0 ? 'bg-warning' : 'bg-success' }}">
                                            {{ $data['areaVariationCount'] ?? 0 }}
                                            ({{ $data['areaVariationPercentage'] ?? 0 }}%)
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge {{ ($data['usageVariationCount'] ?? 0) > 0 ? 'bg-info' : 'bg-success' }}">
                                            {{ $data['usageVariationCount'] ?? 0 }}
                                            ({{ $data['usageVariationPercentage'] ?? 0 }}%)
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('corporation.ward.map', $data['ward_no']) }}"
                                           class="btn btn-primary btn-sm">

                                            <i class="fas fa-map-marked-alt"></i>
                                            View Map

                                        </a>
                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No ward data available
                                    </td>
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

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const chartData = @json($chartData ?? []);

    console.log("Chart Data:", chartData);

    // =========================
    // NO DATA CHECK
    // =========================

    if (!chartData || chartData.length === 0) {

        document.querySelector('#areaVariationChart').parentElement.innerHTML =
            `<div class="alert alert-info text-center">
                No chart data available
            </div>`;

        document.querySelector('#pieChart').parentElement.innerHTML =
            `<div class="alert alert-info text-center">
                No pie chart data available
            </div>`;

        return;
    }

    // =========================
    // BAR CHART DATA
    // =========================

    const labels = chartData.map(item =>
        'Ward ' + (item.ward_no ?? item.ward ?? '')
    );

    const areaData = chartData.map(item =>
        Number(item.areaVariationCount ?? item.area_variation ?? 0)
    );

    const usageData = chartData.map(item =>
        Number(item.usageVariationCount ?? item.usage_variation ?? 0)
    );

    // =========================
    // BAR CHART
    // =========================

    const barCtx = document.getElementById('areaVariationChart');

    new Chart(barCtx, {

        type: 'bar',

        data: {

            labels: labels,

            datasets: [

                {
                    label: 'Area Variation',
                    data: areaData,
                    backgroundColor: '#ffc107',
                    borderRadius: 6
                },

                {
                    label: 'Usage Variation',
                    data: usageData,
                    backgroundColor: '#17a2b8',
                    borderRadius: 6
                }

            ]
        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            plugins: {

                legend: {
                    position: 'top'
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {

                            let label = context.dataset.label || '';
                            let value = context.raw || 0;

                            return `${label}: ${value}`;
                        }
                    }
                }
            },

            scales: {

                y: {
                    beginAtZero: true
                }

            }

        }

    });

    // =========================
    // PIE CHART CALCULATIONS
    // =========================

    let totalAreaVariation = 0;
    let totalUsageVariation = 0;

    areaData.forEach(v => totalAreaVariation += v);
    usageData.forEach(v => totalUsageVariation += v);

    const totalBuildings = Number({{ $total_buildings ?? 0 }});

    let noVariation =
        totalBuildings - (totalAreaVariation + totalUsageVariation);

    if (noVariation < 0) {
        noVariation = 0;
    }

    // =========================
    // PIE CHART
    // =========================

    const pieCtx = document.getElementById('pieChart');

    new Chart(pieCtx, {

        type: 'pie',

        data: {

            labels: [
                'No Variation',
                'Area Variation',
                'Usage Variation'
            ],

            datasets: [{

                data: [
                    noVariation,
                    totalAreaVariation,
                    totalUsageVariation
                ],

                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8'
                ],

                borderWidth: 2

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

                            const total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );

                            const value = context.raw;

                            const percentage =
                                ((value / total) * 100).toFixed(1);

                            return `${context.label}: ${value} (${percentage}%)`;
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
}

.stat-card {
    background: rgba(255,255,255,0.96);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 55px;
    height: 55px;
    background: rgba(22,121,171,0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #1679AB;
}

.chart-container {
    position: relative;
    width: 100%;
    height: 350px;
}

.table th {
    background: #102C57;
    color: #fff;
    border: none;
}

.badge {
    padding: 6px 10px;
    font-size: 12px;
}

canvas {
    width: 100% !important;
    height: 100% !important;
}

@media(max-width:768px){

    .stat-card {
        margin-bottom: 15px;
    }

    .chart-container {
        height: 300px;
    }

}

</style>

@endpush
