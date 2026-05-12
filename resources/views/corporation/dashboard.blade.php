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
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_buildings ?? 0 }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Across all wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Area Variation</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_area_variation ?? 0 }}</h2>
                        <small class="text-warning">
                            <i class="fas fa-chart-line"></i> {{ $area_variation_percentage ?? 0 }}% of buildings
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #fff3cd;"><i class="fas fa-arrows-alt" style="color:#ffc107;"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Usage Variation</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $total_usage_variation ?? 0 }}</h2>
                        <small class="text-info">
                            <i class="fas fa-exchange-alt"></i> {{ $usage_variation_percentage ?? 0 }}% of buildings
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #d1ecf1;"><i class="fas fa-building" style="color:#17a2b8;"></i></div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-7">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i>
                        Ward-wise Area & Usage Variation
                    </h5>
                    <canvas id="areaVariationChart" height="280"></canvas>
                </div>
            </div>
            <div class="col-md-5">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>
                        Overall Variation Summary (3D)
                    </h5>
                    <div id="pieChart3D" style="height: 320px; width: 100%;"></div>
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
                                        <span class="badge {{ ($data['areaVariationCount'] ?? 0) > 0 ? 'bg-warning' : 'bg-success' }}">
                                            {{ $data['areaVariationCount'] ?? 0 }}
                                            @if(($data['buildingCount'] ?? 0) > 0)
                                            ({{ $data['areaVariationPercentage'] ?? 0 }}%)
                                            @endif
                                        </span>
                                    <\/td>
                                    <td>
                                        <span class="badge {{ ($data['usageVariationCount'] ?? 0) > 0 ? 'bg-info' : 'bg-success' }}">
                                            {{ $data['usageVariationCount'] ?? 0 }}
                                            @if(($data['buildingCount'] ?? 0) > 0)
                                            ({{ $data['usageVariationPercentage'] ?? 0 }}%)
                                            @endif
                                        </span>
                                    <\/td>
                                    <td>
                                        <a href="{{ route('corporation.ward.map', $data['ward_no']) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-map-marked-alt"></i> View Map
                                        </a>
                                    <\/td>
                                \n
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No ward data available<\/td>
                                \n
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
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
    // Chart data from PHP
    const chartData = @json($chartData ?? []);
    const totalBuildings = {{ $total_buildings ?? 0 }};
    const totalAreaVariation = {{ $total_area_variation ?? 0 }};
    const totalUsageVariation = {{ $total_usage_variation ?? 0 }};

    // Calculate both variations (buildings that have BOTH area and usage variation)
    // You can adjust this logic based on your actual data
    function calculateBothVariations() {
        let bothCount = 0;
        if (chartData.length > 0) {
            chartData.forEach(item => {
                // Buildings that have both variations (estimated minimum)
                bothCount += Math.min(item.area_variation || 0, item.usage_variation || 0);
            });
        }
        return bothCount;
    }

    const bothVariations = calculateBothVariations();
    const onlyAreaVariation = Math.max(0, totalAreaVariation - bothVariations);
    const onlyUsageVariation = Math.max(0, totalUsageVariation - bothVariations);
    const noVariation = Math.max(0, totalBuildings - (onlyAreaVariation + onlyUsageVariation + bothVariations));

    // Bar Chart
    const ctx1 = document.getElementById('areaVariationChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: chartData.map(item => `Ward ${item.ward}`),
            datasets: [
                {
                    label: 'Area Variation',
                    data: chartData.map(item => item.area_variation || 0),
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Usage Variation',
                    data: chartData.map(item => item.usage_variation || 0),
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
                            let total = chartData[context.dataIndex]?.total_buildings || 0;
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}% of ward buildings)`;
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
                        font: { weight: 'bold' }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Wards',
                        font: { weight: 'bold' }
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

    // 3D Pie Chart using ECharts
    const pieChart3D = echarts.init(document.getElementById('pieChart3D'));

    const pieData = [
        { name: 'No Variation', value: noVariation, itemStyle: { color: '#28a745' } },
        { name: 'Only Area Variation', value: onlyAreaVariation, itemStyle: { color: '#ffc107' } },
        { name: 'Only Usage Variation', value: onlyUsageVariation, itemStyle: { color: '#17a2b8' } },
        { name: 'Both Variations', value: bothVariations, itemStyle: { color: '#dc3545' } }
    ].filter(item => item.value > 0); // Remove zero values

    const option = {
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                const percentage = ((params.value / totalBuildings) * 100).toFixed(1);
                return `<strong>${params.name}</strong><br/>
                        Count: ${params.value} buildings<br/>
                        Percentage: ${percentage}%`;
            },
            backgroundColor: 'rgba(0,0,0,0.8)',
            borderColor: '#fff',
            borderWidth: 1,
            textStyle: { color: '#fff', fontSize: 12 }
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: pieData.map(item => item.name),
            textStyle: { color: '#333', fontSize: 11 },
            formatter: function(name) {
                const item = pieData.find(d => d.name === name);
                const percentage = ((item.value / totalBuildings) * 100).toFixed(1);
                return `${name}: ${percentage}%`;
            }
        },
        series: [
            {
                name: 'Variation Summary',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['50%', '50%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    formatter: function(params) {
                        const percentage = ((params.value / totalBuildings) * 100).toFixed(1);
                        return `${params.name}\n${percentage}%`;
                    },
                    fontSize: 11,
                    fontWeight: 'bold',
                    position: 'outside'
                },
                emphasis: {
                    scale: true,
                    label: {
                        show: true,
                        fontSize: 14,
                        fontWeight: 'bold'
                    }
                },
                data: pieData,
                // 3D effect
                animation: true,
                animationDuration: 1000,
                animationEasing: 'cubicOut',
                // Rose type for 3D effect
                roseType: false,
                // Add shadow for 3D effect
                itemStyle: {
                    borderRadius: 8,
                    borderColor: '#fff',
                    borderWidth: 2,
                    shadowBlur: 10,
                    shadowOffsetX: 3,
                    shadowOffsetY: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.3)'
                },
                // Explode effect on hover
                hoverAnimation: true,
                hoverOffset: 10
            }
        ],
        // Add title with total
        title: {
            show: true,
            text: `Total Buildings: ${totalBuildings}`,
            subtext: 'Click on segments for details',
            left: 'center',
            top: 0,
            textStyle: {
                fontSize: 12,
                fontWeight: 'normal',
                color: '#666'
            },
            subtextStyle: {
                fontSize: 10,
                color: '#999'
            }
        },
        grid: {
            containLabel: true
        },
        // Add background color for 3D effect
        backgroundColor: 'transparent'
    };

    pieChart3D.setOption(option);

    // Make chart responsive
    window.addEventListener('resize', function() {
        pieChart3D.resize();
    });

    // Add click event to show detailed alert
    pieChart3D.on('click', function(params) {
        if (params.componentType === 'series') {
            const percentage = ((params.value / totalBuildings) * 100).toFixed(1);
            alert(`${params.name}\nCount: ${params.value} buildings\nPercentage: ${percentage}% of total buildings`);
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

    /* 3D Pie Chart container styles */
    #pieChart3D {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 10px;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 15px;
        }
        .col-md-5, .col-md-7 {
            margin-bottom: 20px;
        }
    }
</style>
@endpush
