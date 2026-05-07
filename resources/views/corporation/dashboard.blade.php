@extends('layouts.commissioner')

@section('title', 'Commissioner Dashboard')

@section('content-panels')
    <!-- DASHBOARD PANEL refined -->
    <div id="dashboardPanel" class="content-panel">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h2 class="fw-bold text-dark"><i class="fas fa-chart-simple me-2" style="color:#1E7F6E;"></i>Executive
                    Dashboard</h2>
                <p class="text-secondary">Real-time civic infrastructure overview · Last updated
                    {{ now()->format('d M, h:i A') }}</p>
            </div>
            <div class="col-auto">
                <div class="bg-white rounded-4 px-4 py-2 shadow-sm"><i class="far fa-calendar-alt me-1 text-teal"></i>
                    {{ now()->format('l, F j') }}</div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary mb-1">Total Wards</h6>
                        <h2 class="fw-bold display-6 mb-0">{{ $ward_count ?? 24 }}</h2><small class="text-success"><i
                                class="fas fa-arrow-up"></i> +2 zones</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary mb-1">Built Assets</h6>
                        <h2 class="fw-bold mb-0">{{ array_sum(array_column($collections ?? [], 'buildingCount')) }}</h2>
                        <small>Buildings + roads</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-city"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary mb-1">GIS Surveyed</h6>
                        <h2 class="fw-bold mb-0">{{ $mis_count ?? 1847 }}</h2><small>Complete polygons</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-draw-polygon"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary mb-1">Infra Index</h6>
                        <h2 class="fw-bold mb-0">88<span style="font-size:1.5rem;">%</span></h2><small>↑ 6% vs last
                            quarter</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="d-flex justify-content-between mb-3 flex-wrap">
                <h5 class="fw-bold"><i class="fas fa-building me-2" style="color:#1E7F6E;"></i>Ward Performance Snapshot
                </h5><span class="badge bg-light text-dark">Zonal overview</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-modern thead-light">
                        <tr>
                            <th>Zone</th>
                            <th>Ward No</th>
                            <th>Buildings</th>
                            <th>Surveyed</th>
                            <th>Roads</th>
                            <th>MIS</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collections ?? [] as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['zone'] ?? 'Central' }}</td>
                                <td>{{ $item['ward_no'] }}</td>
                                <td>{{ $item['buildingCount'] ?? 0 }}</td>
                                <td>{{ $item['surveyedBuildingCount'] ?? 0 }}</td>
                                <td>{{ $item['roadCount'] ?? 0 }}</td>
                                <td>{{ $item['misCount'] ?? 0 }}</td>
                                <td><a href="{{ route('corporation.ward.details', $item['ward_no']) }}"
                                        class="btn btn-sm btn-soft-primary rounded-pill"><i class="fas fa-arrow-right"></i>
                                        Inspect</a></td>
                            </tr>
                        @empty <tr>
                                <td colspan="7" class="text-center text-muted">No ward data yet. Upload via GIS
                                    integration.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- WARDS PANEL modern zone layout -->
    <div id="wardsPanel" class="content-panel" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fas fa-map-marker-alt me-2 text-teal"></i>Ward Directory</h3>
            <div class="badge bg-white text-dark p-2 shadow-sm">{{ count($zonesWithWards ?? []) }} Active Zones</div>
        </div>
        @foreach ($zonesWithWards ?? [] as $zoneData)
            <div class="stat-card p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-teal-light rounded-circle p-2" style="background:#EFF6F5;"><i
                            class="fas fa-star text-teal"></i></div>
                    <h5 class="fw-bold mb-0">Zone {{ $zoneData['zone'] }}</h5><span
                        class="badge badge-completion">{{ count($zoneData['wards'] ?? []) }} wards</span>
                </div>
                <div class="row g-3">
                    @foreach ($zoneData['wards'] as $ward)
                        <div class="col-md-3 col-sm-6">
                            <div class="ward-grid-card text-center p-3"
                                onclick="window.location='{{ route('corporation.ward.details', $ward['ward_no']) }}'">
                                <i class="fas fa-building-circle-check fa-2x mb-2" style="color:#1E7F6E;"></i>
                                <h6 class="fw-bold">Ward {{ $ward['ward_no'] }}</h6>
                                <small class="text-secondary">{{ $ward['buildingCount'] ?? 0 }} structures</small>
                                <div class="mt-2"><span
                                        class="badge bg-light text-dark rounded-pill">{{ $ward['surveyedCount'] ?? 0 }}
                                        surveyed</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- ANALYSIS PANEL with data visualization -->
    <div id="analysisPanel" class="content-panel" style="display: none;">
        <div class="row mb-4">
            <div class="col">
                <h3 class="fw-bold text-dark"><i class="fas fa-chart-column me-2" style="color:#F4A261;"></i>Urban
                    Intelligence</h3>
                <p class="text-secondary">Survey analytics & completion metrics</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="stat-card p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-chart-bar me-2"></i>Building Density by Ward</h5><canvas
                        id="buildingChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stat-card p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-percent me-2"></i>Survey Completion Rate (%)</h5><canvas
                        id="surveyChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-12">
                <div class="stat-card p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-chalkboard-user"></i> Zone-wise Assets Allocation</h5>
                    <canvas id="zoneChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- REPORTS MODERN -->
    <div id="reportsPanel" class="content-panel" style="display: none;">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-print me-2"></i>Custom Report Engine</h5>
                    <form>
                        <div class="mb-3"><label class="form-label fw-semibold">Report Category</label><select
                                class="form-select rounded-pill">
                                <option>Ward Building Summary</option>
                                <option>GIS Completion Report</option>
                                <option>Road Asset Register</option>
                            </select></div>
                        <div class="row mb-3">
                            <div class="col"><label>Date from</label><input type="date"
                                    class="form-control rounded-pill"></div>
                            <div class="col"><label>Date to</label><input type="date"
                                    class="form-control rounded-pill"></div>
                        </div><button class="btn btn-dark w-100 rounded-pill"><i class="fas fa-file-pdf me-2"></i>Generate
                            and Export</button>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="stat-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-folder-open me-2"></i>Recent Governance Reports</h5>
                    <div class="list-group list-group-flush"><a href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-3"><span><i
                                    class="fas fa-chart-simple me-3 text-teal"></i>Quarterly Infra Review Q2
                                2026</span><small class="text-muted">4.2 MB</small></a><a href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-3"><span><i
                                    class="fas fa-map me-3 text-warning"></i>Ward-level GIS Assessment</span><small
                                class="text-muted">2.8 MB</small></a><a href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-3"><span><i
                                    class="fas fa-building me-3 text-success"></i>Property Tax Base Report</span><small
                                class="text-muted">1.5 MB</small></a></div>
                    <hr>
                    <div class="mt-3 text-center"><button class="btn btn-outline-teal rounded-pill px-4"
                            onclick="alert('Exporting consolidated data..')"><i class="fas fa-database me-2"></i>Download
                            Master Dataset</button></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.initCharts = function() {
                const collections = @json($collections ?? []);
                if (!collections.length) return;
                const wardLabels = collections.map(c => 'Ward ' + c.ward_no);
                const buildData = collections.map(c => c.buildingCount || 0);
                const surveyedData = collections.map(c => c.surveyedBuildingCount || 0);
                const totalBuild = collections.map(c => c.buildingCount || 1);
                const rateData = surveyedData.map((s, i) => totalBuild[i] ? ((s / totalBuild[i]) * 100).toFixed(1) : 0);
                const barCtx = document.getElementById('buildingChart')?.getContext('2d');
                if (barCtx) new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: wardLabels,
                        datasets: [{
                            label: 'Total Buildings',
                            data: buildData,
                            backgroundColor: '#1E7F6E',
                            borderRadius: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
                const lineCtx = document.getElementById('surveyChart')?.getContext('2d');
                if (lineCtx) new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: wardLabels,
                        datasets: [{
                            label: 'Completion %',
                            data: rateData,
                            borderColor: '#F4A261',
                            backgroundColor: 'rgba(244,162,97,0.05)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#0B2B40'
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Percentage (%)'
                                }
                            }
                        }
                    }
                });
                const zonesGroup = @json($zonesWithWards ?? []);
                const zoneNames = zonesGroup.map(z => 'Zone ' + z.zone);
                const zoneBuildings = zonesGroup.map(z => (z.wards || []).reduce((sum, w) => sum + (w.buildingCount || 0),
                    0));
                const doughnut = document.getElementById('zoneChart')?.getContext('2d');
                if (doughnut) new Chart(doughnut, {
                    type: 'doughnut',
                    data: {
                        labels: zoneNames,
                        datasets: [{
                            data: zoneBuildings,
                            backgroundColor: ['#1E7F6E', '#0B2B40', '#F4A261', '#E9C46A', '#87A9A1'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            };
            document.querySelector('[data-page="analysis"]').addEventListener('click', function() {
                if (!window.chartsDrawn) setTimeout(() => {
                    if (typeof window.initCharts === 'function') window.initCharts();
                    window.chartsDrawn = true;
                }, 100);
            });
            showPanel('dashboard');
        </script>
    @endpush
