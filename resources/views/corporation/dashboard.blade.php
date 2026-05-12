@extends('layouts.commissioner')

@section('title', 'Dashboard - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

@section('content')
<div class="dashboard-content-area">
    <div class="animate__animated animate__fadeInUp">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold" style="color:#ffffff;">
                <i class="fas fa-tachometer-alt me-2" style="color:#1679AB;"></i>
                Dashboard Overview
            </h3>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>

        <!-- First Row - Basic Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $ward_count }}</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> Active wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_buildings }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Across all wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Area Variation</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_area_variation }}</h2>
                        <small class="text-warning">
                            <i class="fas fa-chart-line"></i> {{ $area_variation_percentage }}% of buildings
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #fff3cd;"><i class="fas fa-arrows-alt" style="color:#ffc107;"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Usage Variation</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_usage_variation }}</h2>
                        <small class="text-info">
                            <i class="fas fa-exchange-alt"></i> {{ $usage_variation_percentage }}% of buildings
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #d1ecf1;"><i class="fas fa-building" style="color:#17a2b8;"></i></div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i>
                        Ward-wise Area Variation
                    </h5>
                    <canvas id="areaVariationChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>
                        Overall Variation Summary
                    </h5>
                    <canvas id="summaryChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Ward-wise Statistics Table -->
        <div class="row">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                        Ward-wise Statistics
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="wardsTable">
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
                                    <td>{{ $data['buildingCount'] }}</td>
                                    <td>{{ $data['surveyedBuildingCount'] }}</td>
                                    <td>
                                        <span class="badge {{ $data['areaVariationCount'] > 0 ? 'bg-warning' : 'bg-success' }}">
                                            {{ $data['areaVariationCount'] }}
                                            @if($data['buildingCount'] > 0)
                                            ({{ $data['areaVariationPercentage'] }}%)
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $data['usageVariationCount'] > 0 ? 'bg-info' : 'bg-success' }}">
                                            {{ $data['usageVariationCount'] }}
                                            @if($data['buildingCount'] > 0)
                                            ({{ $data['usageVariationPercentage'] }}%)
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('corporation.ward.map', $data['ward_no']) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-map-marked-alt"></i> View Map
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No ward data available</td>
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
    // Chart data from PHP
    const chartData = {!! $chartData !!};

    // Area Variation Bar Chart
    const ctx1 = document.getElementById('areaVariationChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: chartData.map(item => item.ward),
            datasets: [
                {
                    label: 'Area Variation',
                    data: chartData.map(item => item.area_variation),
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Usage Variation',
                    data: chartData.map(item => item.usage_variation),
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw || 0;
                            let total = chartData[context.dataIndex].total_buildings;
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}% of total buildings)`;
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
                        text: 'Wards',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        rotate: 45,
                        autoSkip: true,
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Summary Pie Chart
    const totalAreaVariation = {{ $total_area_variation }};
    const totalUsageVariation = {{ $total_usage_variation }};
    const totalBuildings = {{ $total_buildings }};
    const noVariation = totalBuildings - (totalAreaVariation + totalUsageVariation - getBothVariationCount());

    function getBothVariationCount() {
        // Calculate buildings with both variations
        let bothCount = 0;
        chartData.forEach(item => {
            // Buildings with both variations (estimated)
            bothCount += Math.min(item.area_variation, item.usage_variation);
        });
        return bothCount;
    }

    const bothVariation = getBothVariationCount();
    const onlyAreaVariation = totalAreaVariation - bothVariation;
    const onlyUsageVariation = totalUsageVariation - bothVariation;
    const noVariationCount = totalBuildings - (onlyAreaVariation + onlyUsageVariation + bothVariation);

    const ctx2 = document.getElementById('summaryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['No Variation', 'Only Area Variation', 'Only Usage Variation', 'Both Variations'],
            datasets: [{
                data: [noVariationCount, onlyAreaVariation, onlyUsageVariation, bothVariation],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#dc3545'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let percentage = totalBuildings > 0 ? ((value / totalBuildings) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>

<style>
    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: rgba(22, 121, 171, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #1679AB;
    }

    .table th {
        background: #102C57;
        color: white;
        border: none;
    }

    .badge {
        font-size: 12px;
        padding: 5px 10px;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 15px;
        }
    }
</style>
@endpush
