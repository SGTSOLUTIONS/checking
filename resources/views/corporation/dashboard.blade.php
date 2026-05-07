@extends('layouts.commissioner')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Commissioner Dashboard</h4>
                    <p class="text-muted mb-0">
                        Welcome back, {{ $dashboardData['user']->name ?? 'Commissioner' }}!
                        <small class="text-muted ms-2">
                            <i class="fas fa-clock"></i> Last updated: {{ $dashboardData['last_updated']->format('d M Y, h:i A') }}
                        </small>
                    </p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <button class="btn btn-outline-primary me-2" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 fw-semibold">Total Buildings</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($dashboardData['statistics']['total_buildings'] ?? 0) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +12% from last month
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 fw-semibold">Total Tax Collected</p>
                            <h3 class="mb-0 fw-bold">₹{{ number_format($dashboardData['statistics']['total_tax_collection'] ?? 0, 2) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +8% from last month
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-rupee-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 fw-semibold">Outstanding Balance</p>
                            <h3 class="mb-0 fw-bold text-danger">₹{{ number_format($dashboardData['statistics']['total_balance'] ?? 0, 2) }}</h3>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Needs attention
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 fw-semibold">Collection Efficiency</p>
                            <h3 class="mb-0 fw-bold">{{ $dashboardData['statistics']['collection_efficiency'] ?? 0 }}%</h3>
                            <small class="text-info">
                                <i class="fas fa-chart-line"></i> Target: 85%
                            </small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-percent fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Water Tax Collection</p>
                            <h4 class="mb-0 fw-bold">₹{{ number_format($dashboardData['statistics']['total_water_tax'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-water text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Professional Tax</p>
                            <h4 class="mb-0 fw-bold">₹{{ number_format($dashboardData['statistics']['total_professional_tax'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-briefcase text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">GST Collection</p>
                            <h4 class="mb-0 fw-bold">₹{{ number_format($dashboardData['statistics']['total_gst'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Shops</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($dashboardData['statistics']['total_shops'] ?? 0) }}</h4>
                        </div>
                        <div class="stat-icon bg-secondary bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-store text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-12 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Buildings by Zone</h5>
                </div>
                <div class="card-body">
                    <canvas id="zoneChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Tax Collection Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="taxChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Top Defaulters -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-12 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Recent Activities</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($dashboardData['recent_activities'] ?? [] as $activity)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 me-3" style="background: rgba(54, 162, 235, 0.1);">
                                        <i class="fas {{ $activity['icon'] ?? 'fa-building' }} text-primary"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary mb-1">{{ $activity['type'] }}</span>
                                        <p class="mb-0">{{ $activity['description'] }}</p>
                                        <small class="text-muted">{{ $activity['location'] }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
                                    @if($activity['balance'] > 0)
                                        <span class="badge bg-danger d-block mt-1">Due: ₹{{ number_format($activity['balance'], 2) }}</span>
                                    @else
                                        <span class="badge bg-success d-block mt-1">Paid</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No recent activities found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Top Defaulters</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Owner Name</th>
                                    <th>Door No</th>
                                    <th>Assessment</th>
                                    <th>Balance Due</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dashboardData['top_defaulters'] ?? [] as $defaulter)
                                <tr>
                                    <td class="fw-semibold">{{ $defaulter->name }}</td>
                                    <td>{{ $defaulter->door_no }}</td>
                                    <td>₹{{ number_format($defaulter->assessment, 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format($defaulter->balance, 2) }}</td>
                                    <td><span class="badge bg-warning">{{ $defaulter->type }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                        No defaulters found
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

    <!-- Usage Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Building Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Usage Type</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $usageData = $dashboardData['statistics']['by_usage']['buildings'] ?? [];
                                    $totalUsage = is_array($usageData) ? array_sum($usageData) : 0;
                                @endphp

                                @forelse($usageData as $type => $count)
                                <tr>
                                    <td class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                                    <td>{{ number_format($count) }}</td>
                                    <td>{{ $totalUsage > 0 ? round(($count/$totalUsage)*100, 1) : 0 }}%</td>
                                    <td style="width: 50%;">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: {{ $totalUsage > 0 ? ($count/$totalUsage)*100 : 0 }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-chart-pie fa-2x mb-2 d-block"></i>
                                        No usage data available
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

    <!-- MIS Data Table -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">MIS Records (Old Data)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="misTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ward No</th>
                                    <th>Owner Name</th>
                                    <th>Old Door No</th>
                                    <th>New Door No</th>
                                    <th>Assessment</th>
                                    <th>Half Year Tax</th>
                                    <th>Balance</th>
                                    <th>Zone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dashboardData['mis_data'] ?? [] as $mis)
                                <tr>
                                    <td>{{ $mis->id }}</td>
                                    <td>{{ $mis->ward_no ?? 'N/A' }}</td>
                                    <td class="fw-semibold">{{ $mis->owner_name ?? 'N/A' }}</td>
                                    <td>{{ $mis->old_door_no ?? 'N/A' }}</td>
                                    <td>{{ $mis->new_door_no ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($mis->assessment ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($mis->half_year_tax ?? 0, 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format($mis->balance ?? 0, 2) }}</td>
                                    <td><span class="badge bg-info">{{ $mis->zone ?? 'N/A' }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDetails('mis', {{ $mis->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-database fa-2x mb-2 d-block"></i>
                                        No MIS data found
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

    <!-- Building Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Building Data (New Records)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="buildingTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Owner Name</th>
                                    <th>Door No</th>
                                    <th>Assessment</th>
                                    <th>Half Year Tax</th>
                                    <th>Water Tax</th>
                                    <th>Balance</th>
                                    <th>Phone</th>
                                    <th>Zone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dashboardData['point_data'] ?? [] as $building)
                                <tr>
                                    <td>{{ $building->id }}</td>
                                    <td class="fw-semibold">{{ $building->owner_name ?? $building->present_owner_name ?? 'N/A' }}</td>
                                    <td>{{ $building->new_door_no ?? $building->old_door_no ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($building->assessment ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($building->halfyeartax ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($building->water_tax ?? 0, 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format($building->balance ?? 0, 2) }}</td>
                                    <td>{{ $building->phone_number ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $building->zone ?? 'N/A' }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDetails('building', {{ $building->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                        No building data found
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Data Type</label>
                        <select class="form-select" id="exportType" required>
                            <option value="">Choose...</option>
                            <option value="mis">MIS Data (Old Records)</option>
                            <option value="buildings">Building Data (New Records)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Format</label>
                        <select class="form-select" id="exportFormat">
                            <option value="csv">CSV</option>
                            <option value="excel" disabled>Excel (Coming Soon)</option>
                            <option value="pdf" disabled>PDF (Coming Soon)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="exportData()">Export</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table > :not(caption) > * > * {
        padding: 12px 8px;
    }

    .btn-outline-info:hover {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: white;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
        }

        .stat-icon i {
            font-size: 1.5rem;
        }

        h3 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Initialize DataTables
    $(document).ready(function() {
        if ($('#misTable tbody tr').length > 0) {
            $('#misTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No MIS data available"
                },
                order: [[0, 'desc']]
            });
        }

        if ($('#buildingTable tbody tr').length > 0) {
            $('#buildingTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No building data available"
                },
                order: [[0, 'desc']]
            });
        }
    });

    // Zone Chart - FIXED to handle arrays properly
    const zoneCtx = document.getElementById('zoneChart').getContext('2d');
    let zoneData = @json($dashboardData['statistics']['by_zone']['buildings'] ?? []);

    // Ensure zoneData is an object/array
    const zoneLabels = Object.keys(zoneData || {});
    const zoneValues = Object.values(zoneData || {});

    if (zoneLabels.length > 0) {
        new Chart(zoneCtx, {
            type: 'bar',
            data: {
                labels: zoneLabels,
                datasets: [{
                    label: 'Number of Buildings',
                    data: zoneValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
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
                                return `Buildings: ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        title: {
                            display: true,
                            text: 'Number of Buildings'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Zone'
                        }
                    }
                }
            }
        });
    } else {
        zoneCtx.canvas.parentNode.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-2 d-block"></i>No zone data available</div>';
    }

    // Tax Chart
    const taxCtx = document.getElementById('taxChart').getContext('2d');
    const taxData = {
        labels: ['Half Year Tax', 'Water Tax', 'Professional Tax', 'GST'],
        values: [
            {{ $dashboardData['statistics']['total_tax_collection'] ?? 0 }},
            {{ $dashboardData['statistics']['total_water_tax'] ?? 0 }},
            {{ $dashboardData['statistics']['total_professional_tax'] ?? 0 }},
            {{ $dashboardData['statistics']['total_gst'] ?? 0 }}
        ]
    };

    const hasTaxData = taxData.values.some(v => v > 0);

    if (hasTaxData) {
        new Chart(taxCtx, {
            type: 'doughnut',
            data: {
                labels: taxData.labels,
                datasets: [{
                    data: taxData.values,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
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
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ₹${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        taxCtx.canvas.parentNode.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-2 d-block"></i>No tax data available</div>';
    }

    // View Details Function
    function viewDetails(type, id) {
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching details',
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route("corporation.commissioner.building.details", "") }}/' + id,
            method: 'GET',
            data: { type: type },
            success: function(response) {
                Swal.close();
                if (response.success && response.data) {
                    const data = response.data;
                    let html = '<div class="text-start">';
                    html += '<table class="table table-sm">';
                    for (let [key, value] of Object.entries(data)) {
                        if (value && typeof value !== 'object') {
                            html += `<tr><th>${key.replace(/_/g, ' ').toUpperCase()}</th><td>${value}</td></tr>`;
                        }
                    }
                    html += '</table></div>';

                    Swal.fire({
                        title: `${type.toUpperCase()} Details`,
                        html: html,
                        icon: 'info',
                        width: '800px',
                        confirmButtonText: 'Close'
                    });
                } else {
                    Swal.fire('Error', 'No data found', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch details', 'error');
            }
        });
    }

    // Export Data Function
    function exportData() {
        const exportType = document.getElementById('exportType').value;
        const exportFormat = document.getElementById('exportFormat').value;

        if (!exportType) {
            Swal.fire('Error', 'Please select data type to export', 'error');
            return;
        }

        Swal.fire({
            title: 'Exporting...',
            text: 'Please wait while we prepare your data',
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        window.location.href = '{{ route("corporation.commissioner.export", "") }}/' + exportType + '?format=' + exportFormat;

        setTimeout(() => {
            Swal.close();
            $('#exportModal').modal('hide');
            Swal.fire('Success', 'Export completed successfully', 'success');
        }, 2000);
    }

    // Refresh Data Function
    function refreshData() {
        Swal.fire({
            title: 'Refreshing Data',
            text: 'Please wait while we refresh the dashboard',
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    // Auto refresh every 5 minutes (only if page is visible)
    let autoRefreshInterval = setInterval(function() {
        if (!document.hidden) {
            refreshData();
        }
    }, 300000);

    // Clear interval on page unload
    window.addEventListener('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    });
</script>
@endpush
