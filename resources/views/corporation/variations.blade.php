{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.app')

@section('title', 'Ward ' . $ward_no . ' - Area & Usage Variations')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Ward {{ $warddetail->ward_no }} - {{ $warddetail->zone }} Zone
                            <small class="text-white-50">Variation Analysis Report</small>
                        </h5>
                        <div>
                            <button onclick="window.print()" class="btn btn-light btn-sm">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Corporation ID:</strong> {{ $warddetail->corporation_id }}
                        </div>
                        <div class="col-md-3">
                            <strong>Ward Number:</strong> {{ $warddetail->ward_no }}
                        </div>
                        <div class="col-md-3">
                            <strong>Zone:</strong> {{ ucfirst($warddetail->zone) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Buildings:</strong> {{ count($results) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    @php
        $totalDroneArea = collect($results)->sum('drone_area');
        $totalMisArea = collect($results)->sum('mis_total_area');
        $totalDifference = $totalDroneArea - $totalMisArea;
        $excessCount = collect($results)->where('area_variation', 'EXCESS')->count();
        $shortCount = collect($results)->where('area_variation', 'SHORT')->count();
        $matchedCount = collect($results)->where('area_variation', 'MATCHED')->count();
        $usageVariationCount = collect($results)->where('usage_variation', true)->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Drone Area</h6>
                    <h3 class="mb-0">{{ number_format($totalDroneArea, 2) }} sq.ft</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Total MIS Area</h6>
                    <h3 class="mb-0">{{ number_format($totalMisArea, 2) }} sq.ft</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $totalDifference > 0 ? 'bg-danger' : 'bg-success' }} text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Difference</h6>
                    <h3 class="mb-0">{{ number_format($totalDifference, 2) }} sq.ft</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6 class="card-title">Status</h6>
                    <h3 class="mb-0">
                        @if($totalDifference > 0)
                            OVER
                        @elseif($totalDifference < 0)
                            UNDER
                        @else
                            MATCHED
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Variation Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Area Variation Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="bg-danger text-white p-3 rounded">
                                <h4 class="mb-0">{{ $excessCount }}</h4>
                                <small>EXCESS (>100 sq.ft)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-success text-white p-3 rounded">
                                <h4 class="mb-0">{{ $shortCount }}</h4>
                                <small>SHORT (<-100 sq.ft)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-info text-white p-3 rounded">
                                <h4 class="mb-0">{{ $matchedCount }}</h4>
                                <small>MATCHED</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Usage Variation Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-warning text-dark p-3 rounded">
                                <h4 class="mb-0">{{ $usageVariationCount }}</h4>
                                <small>Buildings with Usage Mismatch</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success text-white p-3 rounded">
                                <h4 class="mb-0">{{ count($results) - $usageVariationCount }}</h4>
                                <small>Buildings with Matching Usage</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Building-wise Detailed Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="variationsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>GIS ID</th>
                                    <th>Building Name</th>
                                    <th>Road Name</th>
                                    <th>Building Usage</th>
                                    <th>Floors</th>
                                    <th>Drone Area (sq.ft)</th>
                                    <th>MIS Area (sq.ft)</th>
                                    <th>Difference</th>
                                    <th>Area Status</th>
                                    <th>Usage Status</th>
                                    <th>Assessments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $result)
                                    <tr>
                                        <td>{{ $result['gisid'] }}</td>
                                        <td>{{ $result['building_name'] ?: 'N/A' }}</td>
                                        <td>{{ $result['road_name'] ?: 'N/A' }}</td>
                                        <td>{{ ucfirst($result['building_usage'] ?: 'N/A') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">F: {{ $result['number_floor'] }}</span>
                                            @if($result['basement'] > 0)
                                                <span class="badge bg-info">B: {{ $result['basement'] }}</span>
                                            @endif
                                            @if($result['percentage'] > 0)
                                                <span class="badge bg-warning">{{ $result['percentage'] }}%</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($result['drone_area'], 2) }}</td>
                                        <td>{{ number_format($result['mis_total_area'], 2) }}</td>
                                        <td class="{{ $result['area_difference'] > 0 ? 'text-danger' : ($result['area_difference'] < 0 ? 'text-success' : '') }}">
                                            {{ number_format($result['area_difference'], 2) }}
                                        </td>
                                        <td>
                                            @if($result['area_variation'] == 'EXCESS')
                                                <span class="badge bg-danger">EXCESS</span>
                                            @elseif($result['area_variation'] == 'SHORT')
                                                <span class="badge bg-success">SHORT</span>
                                            @else
                                                <span class="badge bg-info">MATCHED</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($result['usage_variation'])
                                                <span class="badge bg-warning text-dark">MISMATCH</span>
                                            @else
                                                <span class="badge bg-success">MATCHED</span>
                                            @endif
                                        </td>
                                        <td>{{ $result['assessment_count'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $loop->index }}">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal for Details -->
                                    <div class="modal fade" id="detailsModal{{ $loop->index }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">GIS ID: {{ $result['gisid'] }} - {{ $result['building_name'] }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Building Details -->
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-secondary text-white">
                                                            <h6 class="mb-0">Building Information</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <strong>GIS ID:</strong> {{ $result['gisid'] }}<br>
                                                                    <strong>Building Name:</strong> {{ $result['building_name'] ?: 'N/A' }}<br>
                                                                    <strong>Road Name:</strong> {{ $result['road_name'] ?: 'N/A' }}<br>
                                                                    <strong>Building Usage:</strong> {{ ucfirst($result['building_usage'] ?: 'N/A') }}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Polygon Area:</strong> {{ number_format($result['sqfeet'], 2) }} sq.ft<br>
                                                                    <strong>Number of Floors:</strong> {{ $result['number_floor'] }}<br>
                                                                    <strong>Basement:</strong> {{ $result['basement'] }}<br>
                                                                    <strong>Percentage:</strong> {{ $result['percentage'] }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Area Variation Details -->
                                                    <div class="card mb-3">
                                                        <div class="card-header {{ $result['area_variation'] == 'EXCESS' ? 'bg-danger' : ($result['area_variation'] == 'SHORT' ? 'bg-success' : 'bg-info') }} text-white">
                                                            <h6 class="mb-0">Area Variation Analysis</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <strong>Drone Calculated Area:</strong><br>
                                                                    <span class="h5">{{ number_format($result['drone_area'], 2) }} sq.ft</span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>MIS Total Area:</strong><br>
                                                                    <span class="h5">{{ number_format($result['mis_total_area'], 2) }} sq.ft</span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Difference:</strong><br>
                                                                    <span class="h5 {{ $result['area_difference'] > 0 ? 'text-danger' : ($result['area_difference'] < 0 ? 'text-success' : '') }}">
                                                                        {{ number_format($result['area_difference'], 2) }} sq.ft
                                                                    </span>
                                                                    <br>
                                                                    <span class="badge {{ $result['area_variation'] == 'EXCESS' ? 'bg-danger' : ($result['area_variation'] == 'SHORT' ? 'bg-success' : 'bg-info') }}">
                                                                        {{ $result['area_variation'] }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Usage Mismatches -->
                                                    @if($result['usage_variation'] && count($result['usage_mismatches']) > 0)
                                                        <div class="card mb-3">
                                                            <div class="card-header bg-warning">
                                                                <h6 class="mb-0">Usage Mismatches</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Assessment No.</th>
                                                                                <th>Survey Usage</th>
                                                                                <th>MIS Usage</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($result['usage_mismatches'] as $mismatch)
                                                                                <tr class="table-warning">
                                                                                    <td>{{ $mismatch['assessment'] }}</td>
                                                                                    <td>{{ ucfirst($mismatch['survey_usage'] ?: 'N/A') }}</td>
                                                                                    <td>{{ ucfirst($mismatch['mis_usage'] ?: 'N/A') }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Assessments List -->
                                                    <div class="card">
                                                        <div class="card-header bg-primary text-white">
                                                            <h6 class="mb-0">Assessment Details ({{ $result['assessment_count'] }} records)</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive" style="max-height: 400px;">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Assessment</th>
                                                                            <th>Owner Name</th>
                                                                            <th>Plot Area</th>
                                                                            <th>Half Year Tax</th>
                                                                            <th>Survey Usage</th>
                                                                            <th>MIS Usage</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($result['assessments'] as $assessment)
                                                                            <tr>
                                                                                <td>{{ $assessment->assessment }}</td>
                                                                                <td>{{ $assessment->mis_owner_name ?: 'N/A' }}</td>
                                                                                <td>{{ number_format($assessment->mis_plot_area ?? 0, 2) }}</td>
                                                                                <td>{{ number_format($assessment->mis_half_year_tax ?? 0, 2) }}</td>
                                                                                <td class="{{ (strtolower(trim($assessment->bill_usage ?? '')) != strtolower(trim($assessment->mis_usage ?? ''))) ? 'table-warning' : '' }}">
                                                                                    {{ ucfirst($assessment->bill_usage ?: 'N/A') }}
                                                                                </td>
                                                                                <td class="{{ (strtolower(trim($assessment->bill_usage ?? '')) != strtolower(trim($assessment->mis_usage ?? ''))) ? 'table-warning' : '' }}">
                                                                                    {{ ucfirst($assessment->mis_usage ?: 'N/A') }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No variation data found for this ward.</td>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        var table = document.getElementById("variationsTable");
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.table_to_sheet(table, { raw: true });
        XLSX.utils.book_append_sheet(wb, ws, "Ward_Variations");
        XLSX.writeFile(wb, "ward_{{ $ward_no }}_variations.xlsx");
    }
</script>
@endsection

@endsection
