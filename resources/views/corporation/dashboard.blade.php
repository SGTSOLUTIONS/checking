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
            <div class="col-md-7">
                <div class="stat-card p-3">

                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar me-2" style="color:#1679AB;"></i>
                        Ward-wise Area & Usage Variation
                    </h5>

                    <div style="height:350px;">
                        <canvas id="areaVariationChart"></canvas>
                    </div>

                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-md-5">
                <div class="stat-card p-3">

                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2" style="color:#1679AB;"></i>
                        Overall Variation Summary
                    </h5>

                    <div id="pieChart3D"></div>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Get chart data from PHP (now it's passed as array, not JSON string)
    const chartData = @json($chartData);

    console.log('Chart Data received:', chartData);
    console.log('Chart Data length:', chartData ? chartData.length : 0);

    const totalBuildings = Number({{ $total_buildings ?? 0 }});
    const totalAreaVariation = Number({{ $total_area_variation ?? 0 }});
    const totalUsageVariation = Number({{ $total_usage_variation ?? 0 }});

    console.log('Totals:', { totalBuildings, totalAreaVariation, totalUsageVariation });

    // Check if we have data to display
    if (!chartData || chartData.length === 0) {
        console.warn('No chart data available');
        const barChartContainer = document.getElementById('areaVariationChart');
        if (barChartContainer) {
            barChartContainer.parentElement.innerHTML = '<div class="alert alert-info text-center">No variation data available for charts</div>';
        }
        const pieChartContainer = document.getElementById('pieChart3D');
        if (pieChartContainer) {
            pieChartContainer.innerHTML = '<div class="alert alert-info text-center">No building data available for pie chart</div>';
        }
        return;
    }

    // =========================
    // CALCULATIONS FOR PIE CHART
    // =========================

    let bothVariations = 0;

    chartData.forEach(item => {
        const areaVar = Number(item.area_variation || item.areaVariationCount || 0);
        const usageVar = Number(item.usage_variation || item.usageVariationCount || 0);
        bothVariations += Math.min(areaVar, usageVar);
    });

    const onlyAreaVariation = Math.max(0, totalAreaVariation - bothVariations);
    const onlyUsageVariation = Math.max(0, totalUsageVariation - bothVariations);
    const noVariation = Math.max(0, totalBuildings - (onlyAreaVariation + onlyUsageVariation + bothVariations));

    console.log('Pie chart calculations:', {
        bothVariations,
        onlyAreaVariation,
        onlyUsageVariation,
        noVariation
    });

    // =========================
    // BAR CHART
    // =========================

    const areaChart = document.getElementById('areaVariationChart');

    if (areaChart && chartData.length > 0) {
        // Destroy existing chart if it exists
        let existingChart = Chart.getChart(areaChart);
        if (existingChart) {
            existingChart.destroy();
        }

        new Chart(areaChart, {
            type: 'bar',
            data: {
                labels: chartData.map(item => item.ward || 'Ward ' + item.ward_no),
                datasets: [
                    {
                        label: 'Area Variation',
                        data: chartData.map(item => Number(item.area_variation || item.areaVariationCount || 0)),
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    },
                    {
                        label: 'Usage Variation',
                        data: chartData.map(item => Number(item.usage_variation || item.usageVariationCount || 0)),
                        backgroundColor: 'rgba(23, 162, 184, 0.7)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 1,
                        borderRadius: 5
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
                                let total = chartData[context.dataIndex]?.total_buildings || 0;
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Buildings'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Wards'
                        },
                        ticks: {
                            rotate: 45,
                            autoSkip: true
                        }
                    }
                }
            }
        });

        console.log('Bar chart created successfully');
    } else {
        console.error('Bar chart element not found or no data');
    }

    // =========================
    // PIE CHART (3D)
    // =========================

    const pieChartElement = document.getElementById('pieChart3D');

    if (pieChartElement && totalBuildings > 0) {
        const pieChart3D = echarts.init(pieChartElement);

        const pieData = [
            {
                name: 'No Variation',
                value: noVariation,
                itemStyle: { color: '#28a745' }
            },
            {
                name: 'Only Area Variation',
                value: onlyAreaVariation,
                itemStyle: { color: '#ffc107' }
            },
            {
                name: 'Only Usage Variation',
                value: onlyUsageVariation,
                itemStyle: { color: '#17a2b8' }
            },
            {
                name: 'Both Variations',
                value: bothVariations,
                itemStyle: { color: '#dc3545' }
            }
        ].filter(item => item.value > 0);

        if (pieData.length > 0) {
            pieChart3D.setOption({
                tooltip: {
                    trigger: 'item',
                    formatter: function(params) {
                        const percentage = ((params.value / totalBuildings) * 100).toFixed(1);
                        return `<strong>${params.name}</strong><br/>
                                Count: ${params.value} buildings<br/>
                                Percentage: ${percentage}%`;
                    }
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: pieData.map(item => item.name),
                    formatter: function(name) {
                        const item = pieData.find(d => d.name === name);
                        if (item) {
                            const percentage = ((item.value / totalBuildings) * 100).toFixed(1);
                            return `${name}: ${percentage}%`;
                        }
                        return name;
                    }
                },
                series: [{
                    name: 'Variation Summary',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['50%', '50%'],
                    data: pieData,
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
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2,
                        shadowBlur: 10,
                        shadowOffsetX: 3,
                        shadowOffsetY: 3,
                        shadowColor: 'rgba(0, 0, 0, 0.3)'
                    },
                    emphasis: {
                        scale: true,
                        scaleSize: 10,
                        label: {
                            show: true,
                            fontSize: 14,
                            fontWeight: 'bold'
                        }
                    },
                    animation: true,
                    animationDuration: 1000,
                    hoverAnimation: true,
                    hoverOffset: 10
                }],
                title: {
                    show: true,
                    text: `Total Buildings: ${totalBuildings}`,
                    left: 'center',
                    top: 0,
                    textStyle: {
                        fontSize: 12,
                        color: '#666'
                    }
                },
                backgroundColor: 'transparent'
            });

            window.addEventListener('resize', function () {
                pieChart3D.resize();
            });

            // Add click event
            pieChart3D.on('click', function(params) {
                if (params.componentType === 'series' && totalBuildings > 0) {
                    const percentage = ((params.value / totalBuildings) * 100).toFixed(1);
                    alert(`${params.name}\nCount: ${params.value} buildings\nPercentage: ${percentage}% of total buildings`);
                }
            });

            console.log('Pie chart created successfully');
        } else {
            pieChartElement.innerHTML = '<div style="text-align: center; padding: 50px;">No variation data available</div>';
        }
    } else {
        console.warn('Pie chart not showing. totalBuildings:', totalBuildings);
        if (pieChartElement) {
            pieChartElement.innerHTML = '<div style="text-align: center; padding: 50px;">No building data available for pie chart</div>';
        }
    }
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

.table th {
    background: #102C57;
    color: #fff;
    border: none;
}

.badge {
    padding: 6px 10px;
    font-size: 12px;
}

#pieChart3D {
    width: 100%;
    height: 380px;
    background: linear-gradient(135deg, #f5f7fa 0%, #eef2f7 100%);
    border-radius: 12px;
    padding: 10px;
}

canvas#areaVariationChart {
    max-height: 350px;
    width: 100%;
}

@media(max-width:768px){
    .stat-card {
        margin-bottom: 15px;
    }
    #pieChart3D {
        height: 320px;
    }
}
</style>

@endpush
