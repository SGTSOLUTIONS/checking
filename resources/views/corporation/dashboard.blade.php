@extends('layouts.corporation')

@section('title', 'Commissioner Dashboard')

@section('content-panels')
<!-- DASHBOARD PANEL -->
<div id="dashboardPanel" class="content-panel">
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

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Wards</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $ward_count ?? 0 }}</h2>
                        <small class="text-success"><i class="fas fa-flag"></i> Active Wards</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ array_sum(array_column($collections ?? [], 'buildingCount')) }}</h2>
                        <small class="text-success"><i class="fas fa-building"></i> Surveyed</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Road Network</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ array_sum(array_column($collections ?? [], 'roadCount')) }}</h2>
                        <small class="text-info"><i class="fas fa-road"></i> Total Roads</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-road"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Records</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $mis_count ?? 0 }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> Total Entries</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>
        </div>

        <!-- Zones & Wards Overview -->
        <div class="row g-4">
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-layer-group me-2" style="color:#1679AB;"></i>
                        Zones & Wards Overview
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Zone</th>
                                    <th>Ward No</th>
                                    <th>Buildings</th>
                                    <th>Surveyed</th>
                                    <th>Roads</th>
                                    <th>MIS Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collections ?? [] as $item)
                                <tr>
                                    <td>{{ $item['zone'] ?? '' }}</td>
                                    <td>{{ $item['ward_no'] ?? '' }}</td>
                                    <td>{{ $item['buildingCount'] ?? 0 }}</td>
                                    <td>{{ $item['surveyedBuildingCount'] ?? 0 }}</td>
                                    <td>{{ $item['roadCount'] ?? 0 }}</td>
                                    <td>{{ $item['misCount'] ?? 0 }}</td>
                                    <td>
                                        <a href="{{ route('corporation.ward.details', $item['ward_no']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No ward data available</td>
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

<!-- WARDS PANEL -->
<div id="wardsPanel" class="content-panel" style="display: none;">
    <div class="animate__animated animate__fadeInUp">
        <h3 class="fw-bold mb-4" style="color:#ffffff;">
            <i class="fas fa-map-marker-alt me-2" style="color:#1679AB;"></i>
            Ward Management
        </h3>

        @foreach($zonesWithWards ?? [] as $zoneData)
        <div class="stat-card p-4 mb-4">
            <h5 class="fw-bold mb-3" style="color:#1679AB;">
                <i class="fas fa-star-of-life me-2"></i> Zone: {{ $zoneData['zone'] }}
            </h5>
            <div class="row">
                @foreach($zoneData['wards'] as $ward)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="corp-card text-center p-3 ward-list-item" onclick="window.location='{{ route('corporation.ward.details', $ward['ward_no']) }}'">
                        <i class="fas fa-building fa-2x mb-2" style="color:#1679AB;"></i>
                        <h6 class="fw-bold mb-1">Ward {{ $ward['ward_no'] }}</h6>
                        <small class="text-muted">{{ $ward['buildingCount'] ?? 0 }} Buildings</small>
                        <div class="mt-2">
                            <span class="badge bg-primary">{{ $ward['surveyedCount'] ?? 0 }} Surveyed</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- ANALYSIS PANEL -->
<div id="analysisPanel" class="content-panel" style="display: none;">
    <div class="animate__animated animate__fadeInUp">
        <h3 class="fw-bold mb-4" style="color:#ffffff;">
            <i class="fas fa-chart-line me-2" style="color:#1679AB;"></i>
            Analytical Insights
        </h3>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">Ward-wise Building Distribution</h5>
                    <canvas id="buildingChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">Survey Completion Rate</h5>
                    <canvas id="surveyChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3">Zone Performance Overview</h5>
                    <canvas id="zoneChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REPORTS PANEL -->
<div id="reportsPanel" class="content-panel" style="display: none;">
    <div class="animate__animated animate__fadeInUp">
        <h3 class="fw-bold mb-4" style="color:#ffffff;">
            <i class="fas fa-file-alt me-2" style="color:#1679AB;"></i>
            Reports & Downloads
        </h3>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-download me-2 text-primary"></i> Generate Reports</h5>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select">
                                <option>Ward-wise Building Summary</option>
                                <option>Survey Completion Report</option>
                                <option>MIS Data Export</option>
                                <option>Road Network Summary</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100">
                            <i class="fas fa-file-excel me-2"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-history me-2 text-info"></i> Recent Reports</h5>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-pdf me-2 text-danger"></i> Monthly Report - April 2026</span>
                            <small>2.4 MB</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-excel me-2 text-success"></i> Ward Data Export - Mar 2026</span>
                            <small>1.8 MB</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-pdf me-2 text-danger"></i> Survey Progress Report</span>
                            <small>3.1 MB</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize charts when analysis panel is shown
    let chartsInitialized = false;

    document.querySelector('[data-page="analysis"]').addEventListener('click', function() {
        if (!chartsInitialized) {
            setTimeout(initCharts, 200);
            chartsInitialized = true;
        }
    });

    function initCharts() {
        // Building Distribution Chart
        const buildingCtx = document.getElementById('buildingChart')?.getContext('2d');
        if (buildingCtx) {
            const wardLabels = {!! json_encode(array_column($collections ?? [], 'ward_no')) !!};
            const buildingData = {!! json_encode(array_column($collections ?? [], 'buildingCount')) !!};
            new Chart(buildingCtx, {
                type: 'bar',
                data: {
                    labels: wardLabels.map(w => 'Ward ' + w),
                    datasets: [{
                        label: 'Total Buildings',
                        data: buildingData,
                        backgroundColor: '#1679AB',
                        borderRadius: 8,
                        borderColor: '#102C57',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }

        // Survey Completion Chart
        const surveyCtx = document.getElementById('surveyChart')?.getContext('2d');
        if (surveyCtx) {
            const surveyedData = {!! json_encode(array_column($collections ?? [], 'surveyedBuildingCount')) !!};
            const totalData = {!! json_encode(array_column($collections ?? [], 'buildingCount')) !!};
            const completionRates = surveyedData.map((s, i) => totalData[i] > 0 ? ((s / totalData[i]) * 100).toFixed(1) : 0);
            new Chart(surveyCtx, {
                type: 'line',
                data: {
                    labels: surveyedData.map((_, i) => 'Ward ' + (i + 1)),
                    datasets: [{
                        label: 'Survey Completion (%)',
                        data: completionRates,
                        borderColor: '#FFB1B1',
                        backgroundColor: 'rgba(255, 177, 177, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: { responsive: true, scales: { y: { max: 100, title: { display: true, text: 'Percentage (%)' } } } }
            });
        }

        // Zone Performance Chart
        const zoneCtx = document.getElementById('zoneChart')?.getContext('2d');
        if (zoneCtx) {
            const zones = {!! json_encode(array_column($zonesWithWards ?? [], 'zone')) !!};
            const zoneBuildings = zones.map(zone => {
                const zoneData = {!! json_encode($zonesWithWards ?? []) !!};
                const found = zoneData.find(z => z.zone === zone);
                if (found && found.wards) {
                    return found.wards.reduce((sum, w) => sum + (w.buildingCount || 0), 0);
                }
                return 0;
            });
            new Chart(zoneCtx, {
                type: 'doughnut',
                data: {
                    labels: zones,
                    datasets: [{
                        data: zoneBuildings,
                        backgroundColor: ['#1679AB', '#102C57', '#FFB1B1', '#FFCBCB', '#5A6E7A'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        }
    }

    // Show dashboard by default
    showPanel('dashboard');
</script>
@endpush
