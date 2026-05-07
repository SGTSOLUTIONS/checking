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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 fw-semibold">Total Buildings</p>
                            <h3 class="mb-0 fw-bold">{{ number_format((float)($dashboardData['statistics']['total_buildings'] ?? 0)) }}</h3>
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
                            <h3 class="mb-0 fw-bold">₹{{ number_format((float)($dashboardData['statistics']['total_tax_collection'] ?? 0), 2) }}</h3>
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
                            <h3 class="mb-0 fw-bold text-danger">₹{{ number_format((float)($dashboardData['statistics']['total_balance'] ?? 0), 2) }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ number_format((float)($dashboardData['statistics']['collection_efficiency'] ?? 0)) }}%</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-percent fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Water Tax Collection</p>
                            <h4 class="mb-0 fw-bold">₹{{ number_format((float)($dashboardData['statistics']['total_water_tax'] ?? 0), 2) }}</h4>
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
                            <h4 class="mb-0 fw-bold">₹{{ number_format((float)($dashboardData['statistics']['total_professional_tax'] ?? 0), 2) }}</h4>
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
                            <h4 class="mb-0 fw-bold">₹{{ number_format((float)($dashboardData['statistics']['total_gst'] ?? 0), 2) }}</h4>
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
                            <h4 class="mb-0 fw-bold">{{ number_format((float)($dashboardData['statistics']['total_shops'] ?? 0)) }}</h4>
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
                                    <td>₹{{ number_format((float)($mis->assessment ?? 0), 2) }}</td>
                                    <td>₹{{ number_format((float)($mis->half_year_tax ?? 0), 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format((float)($mis->balance ?? 0), 2) }}</td>
                                    <td><span class="badge bg-info">{{ $mis->zone ?? 'N/A' }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDetails('mis', {{ $mis->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center">No MIS data available</td></tr>
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
                                    <td>₹{{ number_format((float)($building->assessment ?? 0), 2) }}</td>
                                    <td>₹{{ number_format((float)($building->halfyeartax ?? 0), 2) }}</td>
                                    <td>₹{{ number_format((float)($building->water_tax ?? 0), 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format((float)($building->balance ?? 0), 2) }}</td>
                                    <td>{{ $building->phone_number ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $building->zone ?? 'N/A' }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDetails('building', {{ $building->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center">No building data available</td></tr>
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

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        if ($('#misTable tbody tr').length > 0 && $('#misTable tbody tr:first td').length == 10) {
            $('#misTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    emptyTable: "No MIS data available"
                }
            });
        }

        if ($('#buildingTable tbody tr').length > 0 && $('#buildingTable tbody tr:first td').length == 10) {
            $('#buildingTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    emptyTable: "No building data available"
                }
            });
        }
    });

    // Zone Chart
    const zoneData = @json($dashboardData['statistics']['by_zone']['buildings'] ?? []);
    const zoneLabels = Object.keys(zoneData);
    const zoneValues = Object.values(zoneData);

    if (zoneLabels.length > 0) {
        new Chart(document.getElementById('zoneChart'), {
            type: 'bar',
            data: {
                labels: zoneLabels,
                datasets: [{
                    label: 'Number of Buildings',
                    data: zoneValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                }]
            }
        });
    }

    // Tax Chart
    new Chart(document.getElementById('taxChart'), {
        type: 'doughnut',
        data: {
            labels: ['Half Year Tax', 'Water Tax', 'Professional Tax', 'GST'],
            datasets: [{
                data: [
                    {{ (float)($dashboardData['statistics']['total_tax_collection'] ?? 0) }},
                    {{ (float)($dashboardData['statistics']['total_water_tax'] ?? 0) }},
                    {{ (float)($dashboardData['statistics']['total_professional_tax'] ?? 0) }},
                    {{ (float)($dashboardData['statistics']['total_gst'] ?? 0) }}
                ],
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
            }]
        }
    });

    function viewDetails(type, id) {
        Swal.fire({
            title: 'Loading...',
            showConfirmButton: false,
            willOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: `/corporation/commissioner/building/${id}?type=${type}`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                if (response.success && response.data) {
                    let html = '<div class="text-start"><table class="table table-sm">';
                    for (let [key, value] of Object.entries(response.data)) {
                        if (value && typeof value !== 'object') {
                            html += `<tr><th>${key.replace(/_/g, ' ').toUpperCase()}</th><td>${value}</td></tr>`;
                        }
                    }
                    html += '</table></div>';
                    Swal.fire({ title: `${type.toUpperCase()} Details`, html: html, width: '800px' });
                } else {
                    Swal.fire('Error', 'No data found', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch details', 'error');
            }
        });
    }

    function exportData() {
        const exportType = document.getElementById('exportType').value;
        if (!exportType) {
            Swal.fire('Error', 'Please select data type', 'error');
            return;
        }
        window.location.href = `/corporation/commissioner/export/${exportType}?format=csv`;
        $('#exportModal').modal('hide');
    }

    function refreshData() {
        Swal.fire({ title: 'Refreshing...', showConfirmButton: false, timer: 1000 });
        setTimeout(() => location.reload(), 1000);
    }
</script>
@endpush
