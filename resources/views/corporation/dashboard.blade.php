<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Commissioner Dashboard | Emaar Civic Analytics</title>
    <!-- Google Fonts + Font Awesome + Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN for optional insights -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fc;
            overflow-x: hidden;
        }

        /* custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* dashboard container */
        .dashboard-wrapper {
            padding: 1.8rem 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* header card */
        .header-glass {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(2px);
            border-radius: 28px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.05), 0 0 0 1px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }

        /* stat cards */
        .stat-card {
            border: none;
            border-radius: 28px;
            transition: all 0.25s ease;
            overflow: hidden;
            position: relative;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: rgba(255,255,255,0.3);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.2);
            font-size: 28px;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -12px rgba(0,0,0,0.15);
        }

        /* ward card (elevated design) */
        .ward-card {
            border: none;
            border-radius: 32px;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .ward-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 36px -12px rgba(0, 0, 0, 0.2);
        }

        .ward-header {
            background: linear-gradient(135deg, #1a2a3f 0%, #0f1a2a 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .info-grid-mini {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .metric-badge {
            background: #f8fafc;
            border-radius: 24px;
            padding: 0.6rem 0.2rem;
            text-align: center;
            transition: all 0.2s;
            border: 1px solid #eef2f6;
        }
        .metric-badge i {
            font-size: 1.4rem;
            margin-bottom: 0.25rem;
            display: inline-block;
        }

        .btn-outline-insight {
            border-radius: 40px;
            padding: 0.4rem 1.2rem;
            font-weight: 500;
            font-size: 0.85rem;
            border: 1.5px solid #e2e8f0;
            background: white;
            transition: all 0.2s;
        }
        .btn-outline-insight:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            transform: scale(0.97);
        }

        .table-custom {
            font-size: 0.8rem;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-custom thead th {
            background: #f1f5f9;
            font-weight: 600;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 0.5rem;
        }
        .table-custom td {
            padding: 0.7rem 0.5rem;
            vertical-align: middle;
            border-color: #ecf3f9;
        }

        .collapse-content {
            border-top: 1px solid #edf2f7;
            margin-top: 1rem;
            padding-top: 1.2rem;
        }

        /* responsive */
        @media (max-width: 768px) {
            .dashboard-wrapper {
                padding: 1rem;
            }
            .ward-header h5 {
                font-size: 1rem;
            }
            .metric-badge h3 {
                font-size: 1.4rem;
            }
        }

        /* floating refresh glow */
        .refresh-glow {
            transition: all 0.2s;
        }
        .refresh-glow:active {
            transform: scale(0.96);
        }

        .text-accent {
            color: #2c6e9e;
        }
        .bg-soft-primary {
            background-color: #eef2ff;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Header Section with Corporation Identity -->
    <div class="header-glass p-4 mb-5">
        <div class="row align-items-center gy-3">
            <div class="col-md-8">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="rounded-circle bg-dark text-white p-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                    <div>
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 mb-2 rounded-pill">Commissioner Console</span>
                        <h1 class="fw-bold mb-1" style="font-size: 1.9rem;">{{ $corporation->corporation_name ?? 'Emaar Civic Corp' }}</h1>
                        <p class="text-secondary-emphasis mb-0"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Smart City Governance · Real-time GIS & MIS Integration</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-outline-secondary rounded-pill px-4 refresh-glow" onclick="location.reload()">
                    <i class="fas fa-arrows-rotate me-2"></i>Sync Data
                </button>
                <p class="small text-muted mt-2 mb-0"><i class="far fa-calendar-alt"></i> Last updated: {{ now()->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card bg-gradient text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Total Administrative Wards</p>
                        <h2 class="display-5 fw-bold">{{ $ward_count }}</h2>
                        <span class="badge bg-white text-dark mt-2 rounded-pill"><i class="fas fa-flag-checkered me-1"></i> Active Zones</span>
                    </div>
                    <div class="stat-icon bg-white bg-opacity-25">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-gradient" style="background: linear-gradient(135deg, #0f5c5f 0%, #0a7e6f 100%); color: white;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Total MIS Financial Records</p>
                        <h2 class="display-5 fw-bold">{{ number_format($mis_count) }}</h2>
                        <span class="badge bg-white text-dark mt-2 rounded-pill"><i class="fas fa-coins"></i> Tax & Assessment</span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-gradient" style="background: linear-gradient(135deg, #b93b3b 0%, #c95a3a 100%); color: white;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Asset & Infrastructure Collections</p>
                        <h2 class="display-5 fw-bold">{{ count($collections) }}</h2>
                        <span class="badge bg-white text-dark mt-2 rounded-pill"><i class="fas fa-database"></i> GIS/MIS Datasets</span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone&Ward Insight: Graphical Quick peek (Chart) -->
    @php
        $zoneStats = [];
        foreach($collections as $c) {
            $zone = $c['zone'] ?? 'unknown';
            if(!isset($zoneStats[$zone])) {
                $zoneStats[$zone] = ['buildings' => 0, 'surveyed' => 0, 'wards' => []];
            }
            $zoneStats[$zone]['buildings'] += $c['buildingCount'];
            $zoneStats[$zone]['surveyed'] += $c['surveyedBuildingCount'];
            $zoneStats[$zone]['wards'][] = $c['ward_no'];
        }
    @endphp

    @if(count($zoneStats) > 0)
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fas fa-chart-pie me-2 text-primary"></i> Zone-wise Coverage Analytics</h5>
                            <p class="text-muted small">Building footfall vs surveyed structures per zone</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-3 px-md-4 pb-4">
                    <canvas id="zoneChart" style="max-height: 280px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Dynamic Wards Data Cards -->
    <div class="row g-4">
        @forelse($collections as $index => $collection)
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="ward-card">
                <div class="ward-header text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="fas fa-city me-2"></i> Zone {{ $collection['zone'] ?? '—' }}  ·  Ward {{ $collection['ward_no'] }}</h5>
                            <span class="badge bg-light text-dark mt-1 rounded-pill"><i class="fas fa-tag"></i> Ward ID: {{ $collection['ward_no'] }}</span>
                        </div>
                        <i class="fas fa-draw-polygon fa-2x opacity-50"></i>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- mini metrics using grid -->
                    <div class="info-grid-mini">
                        <div class="metric-badge">
                            <i class="fas fa-building text-primary"></i>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($collection['buildingCount']) }}</h3>
                            <small class="text-secondary">Total Buildings</small>
                        </div>
                        <div class="metric-badge">
                            <i class="fas fa-clipboard-list text-success"></i>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($collection['surveyedBuildingCount']) }}</h3>
                            <small class="text-secondary">Surveyed</small>
                        </div>
                        <div class="metric-badge">
                            <i class="fas fa-map-pin text-info"></i>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($collection['pointCount']) }}</h3>
                            <small>Geo Points</small>
                        </div>
                        <div class="metric-badge">
                            <i class="fas fa-road text-warning"></i>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($collection['roadCount']) }}</h3>
                            <small>Road Segments</small>
                        </div>
                        <div class="metric-badge">
                            <i class="fas fa-receipt text-danger"></i>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($collection['misCount']) }}</h3>
                            <small>MIS Records</small>
                        </div>
                    </div>

                    <!-- Tables Summary (clean) -->
                    <div class="mt-3 bg-light p-3 rounded-3">
                        <div class="row row-cols-2 row-cols-sm-4 g-2 text-center small">
                            <div class="col"><i class="fas fa-table me-1"></i> Point: <strong class="text-truncate d-inline-block" style="max-width: 90px;">{{ $collection['pointdatatable'] ? substr($collection['pointdatatable'], -15) : 'N/A' }}</strong></div>
                            <div class="col"><i class="fas fa-draw-polygon me-1"></i> Polygon Data</div>
                            <div class="col"><i class="fas fa-shapes me-1"></i> Master Polygon</div>
                            <div class="col"><i class="fas fa-road me-1"></i> Line/Road</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        <button class="btn btn-outline-insight" type="button" data-bs-toggle="collapse" data-bs-target="#wardDetailedCollapse{{ $index }}" aria-expanded="false">
                            <i class="fas fa-chart-simple me-2"></i>Inspect detailed records
                        </button>
                    </div>

                    <!-- Collapsible Deep Data Section -->
                    <div class="collapse mt-4" id="wardDetailedCollapse{{ $index }}">
                        <div class="collapse-content">
                            <!-- TABS for better UX -->
                            <ul class="nav nav-tabs mb-3" id="tab{{ $index }}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#points{{ $index }}" type="button" role="tab"><i class="fas fa-map-marker-alt me-1"></i> Point Data</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#misdata{{ $index }}" type="button" role="tab"><i class="fas fa-file-invoice me-1"></i> MIS Records</button>
                                </li>
                                @if(!empty($collection['surveyedBuildingData']) && count($collection['surveyedBuildingData']) > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#buildings{{ $index }}" type="button" role="tab"><i class="fas fa-home"></i> Surveyed</button>
                                </li>
                                @endif
                            </ul>
                            <div class="tab-content">
                                <!-- Point data pane -->
                                <div class="tab-pane fade show active" id="points{{ $index }}" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-custom align-middle">
                                            <thead>
                                                <tr><th>GIS ID</th><th>Owner Name</th><th>Door No</th><th>Property Ref</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse(($collection['pointData'] ?? []) as $point)
                                                <tr>
                                                    <td class="fw-semibold">{{ $point->gisid ?? $point->id ?? '—' }}</td>
                                                    <td>{{ $point->owner_name ?? '—' }}</td>
                                                    <td>{{ $point->new_door_no ?? $point->door_no ?? '—' }}</td>
                                                    <td><span class="badge bg-secondary">GIS</span></td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-database me-1"></i> No point data available</td></tr>
                                                @endforelse
                                                @if(count($collection['pointData'] ?? []) > 4)
                                                <tr><td colspan="4" class="text-center small text-secondary">+ {{ count($collection['pointData']) - 4 }} more entries. Full dataset available via export</td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- MIS pane -->
                                <div class="tab-pane fade" id="misdata{{ $index }}" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-custom">
                                            <thead><tr><th>Owner Name</th><th>Old Door No</th><th>Tax Balance (₹)</th><th>Assessment Year</th></tr></thead>
                                            <tbody>
                                                @forelse(($collection['misData'] ?? []) as $mis)
                                                <tr>
                                                    <td>{{ $mis->owner_name ?? '—' }}</td>
                                                    <td>{{ $mis->old_door_no ?? '—' }}</td>
                                                    <td class="fw-semibold text-danger">₹ {{ number_format($mis->balance ?? 0, 2) }}</td>
                                                    <td>{{ $mis->financial_year ?? '2024-25' }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="4" class="text-center text-muted">No MIS financial records for this ward</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(($collection['misCount'] ?? 0) > 0)
                                    <div class="alert alert-light mt-2 small"><i class="fas fa-charging-station"></i> Total pending amount (visible) : ₹ {{ number_format(collect($collection['misData'])->sum('balance'),2) }}</div>
                                    @endif
                                </div>
                                @if(!empty($collection['surveyedBuildingData']) && count($collection['surveyedBuildingData']) > 0)
                                <div class="tab-pane fade" id="buildings{{ $index }}" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-custom">
                                            <thead><tr><th>Building ID</th><th>Structure Type</th><th>Survey Status</th></tr></thead>
                                            <tbody>
                                                @foreach(($collection['surveyedBuildingData'] ?? []) as $bld)
                                                <tr><td>{{ $bld->id ?? $bld->building_id ?? '—' }}</td><td>{{ $bld->type ?? 'Residential' }}</td><td><i class="fas fa-check-circle text-success"></i> Completed</td></tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <!-- Quick summary note -->
                            <div class="mt-3 text-end">
                                <small class="text-secondary"><i class="fas fa-database"></i> Tables: Polygon({{ $collection['polygontable'] ? 'Active' : 'N/A' }}) · Road({{ $collection['roadtable'] ? '✓' : '—' }})</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-3 text-end">
                    <span class="badge rounded-pill bg-light text-dark px-3 py-2"><i class="far fa-clock me-1"></i> Last sync: {{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fas fa-draw-polygon fa-3x mb-3 opacity-50"></i>
                <h4>No ward collection datasets found</h4>
                <p class="mb-0">Please verify GIS tables or ward configuration in the system.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Footer note / credits -->
    <div class="mt-5 pt-3 text-center border-top">
        <p class="text-muted small mb-0"><i class="fas fa-chalkboard-user"></i> Emaar Civic Data Platform · Real-time Commissioner View · GIS + MIS integrated dashboard</p>
    </div>
</div>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Zone chart render (building vs surveyed)
    @if(count($zoneStats) > 0)
    (function() {
        const ctx = document.getElementById('zoneChart')?.getContext('2d');
        if(!ctx) return;
        const zones = @json(array_keys($zoneStats));
        const totalBuildings = @json(array_column($zoneStats, 'buildings'));
        const surveyedBuildings = @json(array_column($zoneStats, 'surveyed'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: zones,
                datasets: [
                    {
                        label: 'Total Buildings (GIS)',
                        data: totalBuildings,
                        backgroundColor: 'rgba(44, 110, 158, 0.7)',
                        borderRadius: 12,
                        barPercentage: 0.65,
                        categoryPercentage: 0.8,
                    },
                    {
                        label: 'Surveyed Structures',
                        data: surveyedBuildings,
                        backgroundColor: 'rgba(34, 197, 94, 0.75)',
                        borderRadius: 12,
                        barPercentage: 0.65,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12, weight: '500' } } },
                    tooltip: { backgroundColor: '#1e293b', titleColor: '#f1f5f9', bodyColor: '#cbd5e1' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e9eef3' }, title: { display: true, text: 'Number of Assets', font: { weight: '500' } } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                }
            }
        });
    })();
    @endif

    // additional: side tooltip for cart info - (not needed but keep)
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if(icon && this.getAttribute('aria-expanded') === 'false') {
                icon.classList.remove('fa-chart-simple');
                icon.classList.add('fa-chevron-up');
            } else if(icon) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chart-simple');
            }
        });
    });
</script>
</body>
</html>
