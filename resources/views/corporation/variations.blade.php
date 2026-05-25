{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Building Variations - Ward ' . ($warddetail->ward_no ?? ''))

@section('content')
<div class="dashboard-content-area">
    <div class="animate__animated animate__fadeInUp">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold text-white">
                <i class="fas fa-chart-line me-2" style="color:#1679AB;"></i>
                Building Variations - Ward {{ $warddetail->ward_no ?? '' }}
            </h3>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
                <a href="{{ route('corporation.dashboard') }}" class="btn btn-sm btn-light ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Ward Info Card -->
        <div class="stat-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color:#102C57;">Ward {{ $warddetail->ward_no ?? '' }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-building me-1"></i> Zone: {{ ucfirst($warddetail->zone ?? 'N/A') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-primary p-2">
                        <i class="fas fa-chart-line me-1"></i> Variation Analysis Report
                    </span>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button onclick="exportToExcel()" class="btn btn-sm btn-success ms-2">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buildings</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalBuildings) }}</h2>
                        <small class="text-info"><i class="fas fa-building"></i> In this ward</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Sq. Feet</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalSqfeet, 2) }}</h2>
                        <small class="text-info"><i class="fas fa-vector-square"></i> Built-up area</small>
                    </div>
                    <div class="stat-icon bg-info-subtle"><i class="fas fa-ruler-combined text-info"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Plot Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalMisPlotArea, 2) }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> From MIS records</small>
                    </div>
                    <div class="stat-icon bg-warning-subtle"><i class="fas fa-database text-warning"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Calculated Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalCalculatedArea, 2) }}</h2>
                        <small class="text-danger"><i class="fas fa-calculator"></i> Based on formula</small>
                    </div>
                    <div class="stat-icon bg-danger-subtle"><i class="fas fa-calculator text-danger"></i></div>
                </div>
            </div>
        </div>

        <!-- Variation Summary Card -->
        @php
            $variationColor = $totalAreaVariation >= 0 ? 'success' : 'danger';
            $variationIcon = $totalAreaVariation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            $variationText = $totalAreaVariation >= 0 ? 'Under-assessment' : 'Over-assessment';
        @endphp

        <div class="stat-card p-4 mb-4" style="background: linear-gradient(135deg, {{ $totalAreaVariation >= 0 ? '#d4edda' : '#f8d7da' }} 0%, #ffffff 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3" style="background: {{ $totalAreaVariation >= 0 ? 'rgba(40, 167, 69, 0.2)' : 'rgba(220, 53, 69, 0.2)' }}">
                            <i class="fas {{ $variationIcon }} {{ $totalAreaVariation >= 0 ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color:#102C57;">
                                Total Area Variation:
                                <span class="{{ $totalAreaVariation >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}
                                </span>
                            </h5>
                            <p class="mb-0">
                                <span class="badge {{ $totalAreaVariation >= 0 ? 'bg-success' : 'bg-danger' }} p-2">
                                    <i class="fas {{ $variationIcon }} me-1"></i>
                                    {{ $variationText }} ({{ $avgVariationPercentage >= 0 ? '+' : '' }}{{ number_format($avgVariationPercentage, 2) }}% Average)
                                </span>
                                <small class="text-muted ms-2">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $totalAreaVariation >= 0 ? 'Positive variation indicates potential revenue loss' : 'Negative variation indicates possible over-assessment' }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="progress" style="height: 10px;">
                        @php
                            $progressPercent = min(100, abs($avgVariationPercentage));
                        @endphp
                        <div class="progress-bar {{ $totalAreaVariation >= 0 ? 'bg-success' : 'bg-danger' }}"
                             style="width: {{ $progressPercent }}%">
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        {{ $progressPercent }}% deviation from MIS records
                    </small>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                    Detailed Building Data
                    <span class="badge bg-primary ms-2">{{ $result->total() }} Records</span>
                </h4>

                <!-- Search Filter -->
                <div class="mt-2 mt-sm-0">
                    <input type="text" id="tableSearch" class="form-control" placeholder="Search by GIS ID..." style="width: 250px;">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="variationsTable">
                    <thead style="background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);">
                        <tr>
                            <th class="text-white">S.No</th>
                            <th class="text-white">GIS ID</th>
                            <th class="text-white text-end">Sq. Feet</th>
                            <th class="text-white text-center">Floors</th>
                            <th class="text-white text-center">Floor %</th>
                            <th class="text-white text-center">Basement</th>
                            <th class="text-white text-end">MIS Area</th>
                            <th class="text-white text-end">Calculated</th>
                            <th class="text-white text-end">Variation</th>
                            <th class="text-white text-center">Variation %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($result as $index => $row)
                        @php
                            $variationClass = $row['area_variation'] >= 0 ? 'text-success' : 'text-danger';
                            $variationIcon = $row['area_variation'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                        @endphp
                        <tr>
                            <td>{{ $result->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-dark p-2">
                                    <i class="fas fa-map-pin me-1"></i>
                                    {{ $row['gisid'] }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($row['sqfeet'], 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $row['number_floor'] }}
                                </span>
                            </td>
                            <td class="text-center">{{ number_format($row['percentage'], 1) }}%</td>
                            <td class="text-center">
                                @if($row['basement'] > 0)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        {{ $row['basement'] }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($row['mis_plot_area'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['calculated_area'], 2) }}</td>
                            <td class="text-end {{ $variationClass }}">
                                <i class="fas {{ $variationIcon }} me-1"></i>
                                {{ $row['area_variation'] >= 0 ? '+' : '' }}{{ number_format($row['area_variation'], 2) }}
                            </td>
                            <td class="text-center {{ $variationClass }}">
                                {{ $row['variation_percentage'] >= 0 ? '+' : '' }}{{ number_format($row['variation_percentage'], 2) }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                                <h5>No Data Available</h5>
                                <p class="text-muted mb-0">No variation data found for this ward</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="2"><strong>TOTAL</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalSqfeet, 2) }}</strong></td>
                            <td colspan="3"></td>
                            <td class="text-end"><strong>{{ number_format($totalMisPlotArea, 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalCalculatedArea, 2) }}</strong></td>
                            <td class="text-end {{ $totalAreaVariation >= 0 ? 'text-success' : 'text-danger' }}">
                                <strong>{{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}</strong>
                            </td>
                            <td class="text-center {{ $avgVariationPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                                <strong>{{ $avgVariationPercentage >= 0 ? '+' : '' }}{{ number_format($avgVariationPercentage, 2) }}%</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            @if($result->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                    <div class="mb-2 mb-sm-0">
                        <small class="text-muted">
                            Showing {{ $result->firstItem() ?? 0 }} to {{ $result->lastItem() ?? 0 }} of {{ $result->total() ?? 0 }} records
                        </small>
                    </div>
                    <div>
                        {{ $result->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Info Note with Corrected Formula -->
        <div class="stat-card p-3 mt-4" style="background: #e7f3ff;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-lightbulb fa-2x" style="color:#1679AB;"></i>
                </div>
                <div>
                    <strong class="d-block">Understanding the Calculations</strong>
                    <small class="text-muted">
                        <strong>Formula:</strong> Calculated Area = (Number of Floors + Basement + (Floor % / 100)) × Sq. Feet<br>
                        <strong>Example:</strong> If Floors=2, Basement=1, Floor%=80, Sq.Feet=1000<br>
                        = (2 + 1 + 0.8) × 1000 = 3.8 × 1000 = 3800 sq.ft<br>
                        <strong>Area Variation</strong> = Calculated Area - MIS Plot Area
                        (<span class="text-success">Positive = Under-assessment</span>,
                        <span class="text-danger">Negative = Over-assessment</span>)<br>
                        <strong>Note:</strong> All totals reflect ALL buildings in this ward, not just the current page.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        // Table search functionality
        $("#tableSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#variationsTable tbody tr").filter(function() {
                $(this).toggle($(this).find('td:eq(1)').text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function exportToExcel() {
        const table = document.getElementById('variationsTable');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });

        // Add summary info
        const summaryData = [
            ['BUILDING VARIATION REPORT'],
            ['Generated on:', new Date().toLocaleString()],
            ['Ward No:', '{{ $warddetail->ward_no ?? '' }}'],
            ['Zone:', '{{ ucfirst($warddetail->zone ?? 'N/A') }}'],
            [''],
            ['SUMMARY STATISTICS'],
            ['Total Buildings:', '{{ number_format($totalBuildings) }}'],
            ['Total Sq. Feet:', '{{ number_format($totalSqfeet, 2) }}'],
            ['Total MIS Plot Area:', '{{ number_format($totalMisPlotArea, 2) }}'],
            ['Total Calculated Area:', '{{ number_format($totalCalculatedArea, 2) }}'],
            ['Total Area Variation:', '{{ $totalAreaVariation >= 0 ? "+" : "" }}{{ number_format($totalAreaVariation, 2) }}'],
            ['Average Variation %:', '{{ $avgVariationPercentage >= 0 ? "+" : "" }}{{ number_format($avgVariationPercentage, 2) }}%'],
            ['Status:', '{{ $totalAreaVariation >= 0 ? "Under-assessment" : "Over-assessment" }}'],
            [''],
            ['FORMULA USED:'],
            ['Calculated Area = (Number of Floors + Basement + (Floor % / 100)) × Sq. Feet'],
            [''],
            ['DETAILED DATA']
        ];

        XLSX.utils.sheet_add_aoa(ws, summaryData, { origin: 'A1' });

        // Adjust column widths
        ws['!cols'] = [
            {wch:8}, {wch:14}, {wch:12}, {wch:8},
            {wch:8}, {wch:8}, {wch:12}, {wch:12},
            {wch:12}, {wch:10}
        ];

        XLSX.utils.book_append_sheet(wb, ws, 'Ward_{{ $warddetail->ward_no ?? '' }}_Variations');
        XLSX.writeFile(wb, 'Ward_{{ $warddetail->ward_no ?? '' }}_Building_Variations.xlsx');
    }

    // Print styling
    window.onbeforeprint = function() {
        document.querySelectorAll('.btn, .menu-toggle, .navbar-custom .dropdown').forEach(el => {
            if(el) el.style.display = 'none';
        });
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        if(sidebar) sidebar.style.display = 'none';
        if(mainContent) {
            mainContent.style.width = '100%';
            mainContent.style.marginLeft = '0';
        }
    };

    window.onafterprint = function() {
        location.reload();
    };
</script>

<style>
    .dashboard-content-area {
        padding: 20px;
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        min-height: 100vh;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.96);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        background: rgba(22, 121, 171, 0.1);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #1679AB;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-bottom: none;
        padding: 15px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background: rgba(22, 121, 171, 0.05);
    }

    .table tfoot td {
        padding: 12px 15px;
        background: #f8f9fa;
        border-top: 2px solid #1679AB;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
    }

    .form-control:focus {
        border-color: #1679AB;
        box-shadow: 0 0 0 0.2rem rgba(22, 121, 171, 0.25);
    }

    .btn-sm {
        border-radius: 8px;
        padding: 5px 12px;
    }

    .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 20px;
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        color: #102C57;
        border-radius: 8px;
        margin: 0 2px;
        border: none;
        padding: 8px 14px;
    }

    .page-item.active .page-link {
        background-color: #1679AB;
        border-color: #1679AB;
        color: white;
    }

    .page-link:hover {
        color: #1679AB;
        background-color: #f8f9fa;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate__fadeInUp {
        animation-name: fadeInUp;
        animation-duration: 0.6s;
    }

    @media (max-width: 768px) {
        .dashboard-content-area {
            padding: 15px;
        }

        .stat-card {
            margin-bottom: 15px;
        }

        .table {
            font-size: 0.8rem;
        }

        .table thead th,
        .table tbody td {
            padding: 8px 10px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }

        .stat-card h2 {
            font-size: 1.3rem;
        }

        .stat-card h6 {
            font-size: 0.7rem;
        }
    }
</style>
@endpush
