@extends('layouts.commissioner')

@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-dark">Commissioner Dashboard</h4>
                    <p class="text-muted mb-0">Welcome back, {{ $dashboardData['user']->name ?? 'Commissioner' }}!</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Buildings</p>
                            <h3 class="mb-0">{{ number_format($dashboardData['statistics']['total_buildings'] ?? 0) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +12%
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
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Tax Collected</p>
                            <h3 class="mb-0">₹{{ number_format($dashboardData['statistics']['total_tax_collection'] ?? 0, 2) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +8%
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
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Outstanding Balance</p>
                            <h3 class="mb-0">₹{{ number_format($dashboardData['statistics']['total_balance'] ?? 0, 2) }}</h3>
                            <small class="text-danger">
                                <i class="fas fa-arrow-down"></i> +5%
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
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Shops</p>
                            <h3 class="mb-0">{{ number_format($dashboardData['statistics']['total_shops'] ?? 0) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +3%
                            </small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-store fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Water Tax Collection</p>
                            <h4 class="mb-0">₹{{ number_format($dashboardData['statistics']['total_water_tax'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-water text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Professional Tax</p>
                            <h4 class="mb-0">₹{{ number_format($dashboardData['statistics']['total_professional_tax'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-briefcase text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">GST Collection</p>
                            <h4 class="mb-0">₹{{ number_format($dashboardData['statistics']['total_gst'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">MIS Records</p>
                            <h4 class="mb-0">{{ number_format($dashboardData['statistics']['total_mis_records'] ?? 0) }}</h4>
                        </div>
                        <div class="stat-icon bg-secondary bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-database text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-12 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Buildings by Zone</h5>
                </div>
                <div class="card-body">
                    <canvas id="zoneChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tax Collection Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="taxChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-5 col-md-12 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($dashboardData['recent_activities'] ?? [] as $activity)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">{{ $activity['type'] }}</span>
                                    <span>{{ $activity['description'] }}</span>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted">
                            No recent activities found
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-md-12 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Stats by Usage Type</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Usage Type</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $usageData = $dashboardData['statistics']['by_usage']['buildings'] ?? [];
                                    $total = array_sum($usageData);
                                @endphp
                                @forelse($usageData as $type => $count)
                                <tr>
                                    <td>{{ ucfirst($type) }}</td>
                                    <td>{{ number_format($count) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: {{ $total > 0 ? ($count/$total)*100 : 0 }}%">
                                                {{ $total > 0 ? round(($count/$total)*100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data available</td>
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
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">MIS Records (Old Data)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="misTable">
                            <thead>
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
                                    <td>{{ $mis->owner_name ?? 'N/A' }}</td>
                                    <td>{{ $mis->old_door_no ?? 'N/A' }}</td>
                                    <td>{{ $mis->new_door_no ?? 'N/A' }}</td>
                                    <td>{{ number_format($mis->assessment ?? 0, 2) }}</td>
                                    <td>{{ number_format($mis->half_year_tax ?? 0, 2) }}</td>
                                    <td class="text-danger">{{ number_format($mis->balance ?? 0, 2) }}</td>
                                    <td>{{ $mis->zone ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewDetails('mis', {{ $mis->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No MIS data found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Point Data Table (New Buildings) -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Building Data (New Records)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="buildingTable">
                            <thead>
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
                                    <td>{{ $building->owner_name ?? $building->present_owner_name ?? 'N/A' }}</td>
                                    <td>{{ $building->new_door_no ?? $building->old_door_no ?? 'N/A' }}</td>
                                    <td>{{ number_format($building->assessment ?? 0, 2) }}</td>
                                    <td>{{ number_format($building->halfyeartax ?? 0, 2) }}</td>
                                    <td>{{ number_format($building->water_tax ?? 0, 2) }}</td>
                                    <td class="text-danger">{{ number_format($building->balance ?? 0, 2) }}</td>
                                    <td>{{ $building->phone_number ?? 'N/A' }}</td>
                                    <td>{{ $building->zone ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewDetails('building', {{ $building->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No building data found</td>
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
            <div class="modal-header">
                <h5 class="modal-title">Export Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label">Select Data Type</label>
                        <select class="form-select" id="exportType" required>
                            <option value="">Choose...</option>
                            <option value="mis">MIS Data (Old Records)</option>
                            <option value="buildings">Building Data (New Records)</option>
                            <option value="both">Both Data Types</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select class="form-select" id="exportFormat">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
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
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .card-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    .btn-group {
        gap: 5px;
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
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize DataTables
    $(document).ready(function() {
        $('#misTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });

        $('#buildingTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });
    });

    // Zone Chart
    const zoneCtx = document.getElementById('zoneChart').getContext('2d');
    const zoneData = @json($dashboardData['statistics']['by_zone']['buildings'] ?? []);

    new Chart(zoneCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(zoneData),
            datasets: [{
                label: 'Number of Buildings',
                data: Object.values(zoneData),
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
                            return `Buildings: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
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

    // Tax Chart
    const taxCtx = document.getElementById('taxChart').getContext('2d');

    new Chart(taxCtx, {
        type: 'doughnut',
        data: {
            labels: ['Half Year Tax', 'Water Tax', 'Professional Tax', 'GST'],
            datasets: [{
                data: [
                    {{ $dashboardData['statistics']['total_tax_collection'] ?? 0 }},
                    {{ $dashboardData['statistics']['total_water_tax'] ?? 0 }},
                    {{ $dashboardData['statistics']['total_professional_tax'] ?? 0 }},
                    {{ $dashboardData['statistics']['total_gst'] ?? 0 }}
                ],
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

        // Make AJAX call to get details
        setTimeout(() => {
            Swal.fire({
                title: 'Information',
                text: `Viewing ${type} details for ID: ${id}`,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }, 1000);
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

        // Redirect to export route
        if (exportType !== 'both') {
            window.location.href = `{{ route('corporation.commissioner.export', ['type' => '']) }}/${exportType}?format=${exportFormat}`;
        } else {
            // Handle both exports
            Swal.fire('Info', 'Both data types export will start shortly', 'info');
            setTimeout(() => {
                window.location.href = `{{ route('corporation.commissioner.export', ['type' => 'mis']) }}?format=${exportFormat}`;
                setTimeout(() => {
                    window.location.href = `{{ route('corporation.commissioner.export', ['type' => 'buildings']) }}?format=${exportFormat}`;
                }, 1000);
            }, 1000);
        }

        setTimeout(() => {
            Swal.close();
            $('#exportModal').modal('hide');
            Swal.fire('Success', 'Export completed successfully', 'success');
        }, 2000);
    }

    // Auto refresh every 5 minutes
    setInterval(function() {
        Swal.fire({
            title: 'Refreshing Data',
            text: 'Dashboard will refresh automatically',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.reload();
        });
    }, 300000); // 5 minutes
</script>
@endpush
