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
                <button id="refreshData" class="btn btn-sm btn-primary ms-2">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>

        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">

            <!-- Total Wards -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;" id="totalWards">
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
                        <h2 class="fw-bold mb-0" style="color:#102C57;" id="totalBuildings">
                            0
                        </h2>
                        <small class="text-success">
                            <i class="fas fa-building"></i>
                            Loading...
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
                        <h2 class="fw-bold mb-0" style="color:#102C57;" id="totalAreaVariation">
                            0
                        </h2>
                        <small class="text-warning" id="areaVariationPercent">
                            <i class="fas fa-chart-line"></i>
                            0%
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
                        <h2 class="fw-bold mb-0" style="color:#102C57;" id="totalUsageVariation">
                            0
                        </h2>
                        <small class="text-info" id="usageVariationPercent">
                            <i class="fas fa-exchange-alt"></i>
                            0%
                        </small>
                    </div>
                    <div class="stat-icon bg-info-subtle">
                        <i class="fas fa-building text-info"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- Loading Progress -->
        <div id="loadingProgress" class="alert alert-info" style="display: none;">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <span>Loading ward data... <span id="loadedCount">0</span> / <span id="totalWardsCount">{{ $ward_count }}</span></span>
                <div class="progress flex-grow-1 ms-3" style="height: 20px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%">0%</div>
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
                            <tbody id="wardsTableBody">
                                <!-- Loading skeleton -->
                                @foreach($wards as $ward)
                                <tr id="ward-row-{{ $ward->ward_no }}" data-ward-no="{{ $ward->ward_no }}">
                                    <td>{{ ucfirst($ward->zone) }}</td>
                                    <td>{{ $ward->ward_no }}</td>
                                    <td class="text-muted">
                                        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                    </td>
                                    <td class="text-muted">
                                        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                    </td>
                                    <td class="text-muted">
                                        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                    </td>
                                    <td class="text-muted">
                                        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                    </td>
                                    <td>
                                        <a href="{{ route('corporation.ward.map', $ward->ward_no) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-map-marked-alt"></i>
                                            View Map
                                        </a>
                                        <a href="{{ route('corporation.ward.excel', $ward->ward_no) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i>
                                            Excel
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
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

class DashboardManager {
    constructor() {
        this.wards = [];
        this.wardsData = new Map();
        this.totalBuildings = 0;
        this.totalAreaVariation = 0;
        this.totalUsageVariation = 0;
        this.chartData = [];
        this.barChart = null;
        this.pieChart = null;
        this.currentIndex = 0;
        this.isLoading = false;
    }

    async init() {
        this.setupEventListeners();
        await this.loadAllWardsData();
    }

    setupEventListeners() {
        document.getElementById('refreshData').addEventListener('click', () => {
            this.resetAndReload();
        });
    }

    async loadAllWardsData() {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoadingProgress(true);

        // Get all ward elements from the table
        const wardRows = document.querySelectorAll('#wardsTableBody tr');
        this.wards = Array.from(wardRows).map(row => ({
            id: row.dataset.wardNo,
            element: row
        }));

        const totalWards = this.wards.length;
        document.getElementById('totalWardsCount').textContent = totalWards;

        // Reset totals
        this.resetTotals();

        // Load wards one by one
        for (let i = 0; i < this.wards.length; i++) {
            await this.loadWardData(this.wards[i]);
            this.updateProgress(i + 1, totalWards);
        }

        // Update charts after all data is loaded
        this.updateCharts();
        this.updateSummaryCards();

        this.showLoadingProgress(false);
        this.isLoading = false;
    }

    async loadWardData(ward) {
        try {
            const response = await fetch('{{ route("corporation.ward.data") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ward_no: ward.id })
            });

            const result = await response.json();

            if (result.success) {
                this.updateWardRow(ward.element, result.data);
                this.accumulateTotals(result.data);
                this.chartData.push(result.chartData);
            } else {
                console.error('Error loading ward:', result.error);
                this.showWardError(ward.element);
            }
        } catch (error) {
            console.error('Error fetching ward data:', error);
            this.showWardError(ward.element);
        }
    }

    updateWardRow(row, data) {
        const cells = row.cells;
        cells[2].innerHTML = data.buildingCount || 0;
        cells[3].innerHTML = data.surveyedBuildingCount || 0;

        // Area Variation cell
        const areaVariation = data.areaVariationCount || 0;
        const areaPercent = data.areaVariationPercentage || 0;
        cells[4].innerHTML = `
            <span class="badge ${areaVariation > 0 ? 'bg-warning' : 'bg-success'}">
                ${areaVariation} (${areaPercent}%)
            </span>
        `;

        // Usage Variation cell
        const usageVariation = data.usageVariationCount || 0;
        const usagePercent = data.usageVariationPercentage || 0;
        cells[5].innerHTML = `
            <span class="badge ${usageVariation > 0 ? 'bg-info' : 'bg-success'}">
                ${usageVariation} (${usagePercent}%)
            </span>
        `;

        // Add animation class
        row.classList.add('table-active');
        setTimeout(() => row.classList.remove('table-active'), 500);
    }

    showWardError(row) {
        const cells = row.cells;
        cells[2].innerHTML = '<span class="text-danger">Error</span>';
        cells[3].innerHTML = '<span class="text-danger">Error</span>';
        cells[4].innerHTML = '<span class="badge bg-danger">Error</span>';
        cells[5].innerHTML = '<span class="badge bg-danger">Error</span>';
    }

    accumulateTotals(data) {
        this.totalBuildings += data.buildingCount || 0;
        this.totalAreaVariation += data.areaVariationCount || 0;
        this.totalUsageVariation += data.usageVariationCount || 0;
    }

    resetTotals() {
        this.totalBuildings = 0;
        this.totalAreaVariation = 0;
        this.totalUsageVariation = 0;
        this.chartData = [];
    }

    resetAndReload() {
        // Reset all table rows to loading state
        document.querySelectorAll('#wardsTableBody tr').forEach(row => {
            const cells = row.cells;
            cells[2].innerHTML = '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>';
            cells[3].innerHTML = '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>';
            cells[4].innerHTML = '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>';
            cells[5].innerHTML = '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>';
        });

        // Reset summary cards
        document.getElementById('totalBuildings').textContent = '0';
        document.getElementById('totalAreaVariation').textContent = '0';
        document.getElementById('totalUsageVariation').textContent = '0';
        document.getElementById('areaVariationPercent').innerHTML = '<i class="fas fa-chart-line"></i> 0%';
        document.getElementById('usageVariationPercent').innerHTML = '<i class="fas fa-exchange-alt"></i> 0%';

        // Reload data
        this.loadAllWardsData();
    }

    updateProgress(loaded, total) {
        const percentage = (loaded / total) * 100;
        document.getElementById('loadedCount').textContent = loaded;
        document.getElementById('progressBar').style.width = percentage + '%';
        document.getElementById('progressBar').textContent = Math.round(percentage) + '%';
    }

    showLoadingProgress(show) {
        const progressDiv = document.getElementById('loadingProgress');
        progressDiv.style.display = show ? 'block' : 'none';

        if (!show) {
            document.getElementById('loadedCount').textContent = this.wards.length;
            document.getElementById('progressBar').style.width = '100%';
            document.getElementById('progressBar').textContent = '100%';
        }
    }

    updateSummaryCards() {
        document.getElementById('totalBuildings').textContent = this.totalBuildings.toLocaleString();
        document.getElementById('totalAreaVariation').textContent = this.totalAreaVariation.toLocaleString();
        document.getElementById('totalUsageVariation').textContent = this.totalUsageVariation.toLocaleString();

        const areaPercent = this.totalBuildings > 0
            ? ((this.totalAreaVariation / this.totalBuildings) * 100).toFixed(1)
            : 0;
        const usagePercent = this.totalBuildings > 0
            ? ((this.totalUsageVariation / this.totalBuildings) * 100).toFixed(1)
            : 0;

        document.getElementById('areaVariationPercent').innerHTML = `
            <i class="fas fa-chart-line"></i> ${areaPercent}%
        `;
        document.getElementById('usageVariationPercent').innerHTML = `
            <i class="fas fa-exchange-alt"></i> ${usagePercent}%
        `;
    }

    updateCharts() {
        if (this.chartData.length === 0) return;

        const labels = this.chartData.map(item =>
            'Ward ' + (item.ward_no || item.ward || '')
        );

        const areaData = this.chartData.map(item =>
            Number(item.areaVariationCount || item.area_variation || 0)
        );

        const usageData = this.chartData.map(item =>
            Number(item.usageVariationCount || item.usage_variation || 0)
        );

        // Update Bar Chart
        if (this.barChart) {
            this.barChart.data.labels = labels;
            this.barChart.data.datasets[0].data = areaData;
            this.barChart.data.datasets[1].data = usageData;
            this.barChart.update();
        } else {
            const barCtx = document.getElementById('areaVariationChart');
            this.barChart = new Chart(barCtx, {
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
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Update Pie Chart
        const noVariation = Math.max(0, this.totalBuildings - (this.totalAreaVariation + this.totalUsageVariation));

        if (this.pieChart) {
            this.pieChart.data.datasets[0].data = [noVariation, this.totalAreaVariation, this.totalUsageVariation];
            this.pieChart.update();
        } else {
            const pieCtx = document.getElementById('pieChart');
            this.pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['No Variation', 'Area Variation', 'Usage Variation'],
                    datasets: [{
                        data: [noVariation, this.totalAreaVariation, this.totalUsageVariation],
                        backgroundColor: ['#28a745', '#ffc107', '#17a2b8'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.raw;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new DashboardManager();
    dashboard.init();
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

.table-active {
    background-color: #e7f3ff !important;
    transition: background-color 0.5s;
}

#loadingProgress {
    border-left: 4px solid #0d6efd;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    background-color: #0d6efd;
    transition: width 0.3s ease;
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
