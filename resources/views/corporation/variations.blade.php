{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Building Variations - Ward ' . ($warddetail->ward_no ?? ''))

@section('styles')
<style>
    .variation-positive {
        color: #10b981;
        font-weight: 600;
    }
    .variation-negative {
        color: #ef4444;
        font-weight: 600;
    }
    .summary-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(212, 161, 62, 0.2);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(212, 161, 62, 0.15);
        border-color: #D4A13E;
    }
    .summary-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: inline-block;
    }
    .summary-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    .summary-label {
        color: #6B7A7F;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .total-row {
        background: linear-gradient(135deg, #0B2B40 0%, #1A6B6E 100%);
        color: white;
        font-weight: 700;
    }
    .total-row td {
        color: white;
        font-weight: 700;
        padding: 12px;
    }
    .info-badge {
        background: rgba(212, 161, 62, 0.15);
        color: #D4A13E;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 500;
    }
    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    .table-custom th {
        background: linear-gradient(135deg, #0B2B40, #1A6B6E);
        color: white;
        font-weight: 600;
        border: none;
        padding: 15px;
        font-size: 0.9rem;
    }
    .table-custom td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
    }
    .table-custom tr:hover {
        background-color: rgba(212, 161, 62, 0.05);
    }
    .ward-header {
        background: linear-gradient(135deg, #0B2B40 0%, #1A6B6E 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }
    .breadcrumb-custom {
        background: rgba(255, 255, 255, 0.1);
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
    }
    .breadcrumb-custom a {
        color: #D4A13E;
        text-decoration: none;
    }
    .breadcrumb-custom a:hover {
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .summary-value {
            font-size: 1.2rem;
        }
        .summary-icon {
            font-size: 1.8rem;
        }
        .table-custom {
            font-size: 0.8rem;
        }
        .table-custom th,
        .table-custom td {
            padding: 8px 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="content-panel">
    <!-- Ward Header -->
    <div class="ward-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="breadcrumb-custom mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <a href="{{ route('corporation.dashboard') }}">Dashboard</a>
                    <i class="fas fa-chevron-right mx-2 fa-xs"></i>
                    <span>Ward {{ $warddetail->ward_no }}</span>
                </div>
                <h2 class="fw-bold mb-2">
                    <i class="fas fa-building me-3"></i>
                    Ward {{ $warddetail->ward_no }} - Building Variations
                </h2>
                <p class="mb-0 opacity-75">
                    <i class="fas fa-location-dot me-2"></i>
                    Zone: {{ ucfirst($warddetail->zone) }} |
                    <i class="fas fa-calendar ms-2 me-1"></i>
                    Analysis Date: {{ now()->format('d M, Y') }}
                </p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-light">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
                <a href="{{ route('corporation.dashboard') }}" class="btn btn-outline-light ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-building" style="color: #1A6B6E;"></i>
                </div>
                <div class="summary-value">{{ number_format($totalBuildings) }}</div>
                <div class="summary-label">
                    <i class="fas fa-chart-line me-1"></i>Total Buildings
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-vector-square" style="color: #D4A13E;"></i>
                </div>
                <div class="summary-value">{{ number_format($totalSqfeet, 2) }}</div>
                <div class="summary-label">
                    <i class="fas fa-ruler-combined me-1"></i>Total Sq. Feet
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-draw-polygon" style="color: #E86A5F;"></i>
                </div>
                <div class="summary-value">{{ number_format($totalMisPlotArea, 2) }}</div>
                <div class="summary-label">
                    <i class="fas fa-database me-1"></i>Total MIS Plot Area
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-calculator" style="color: #0B2B40;"></i>
                </div>
                <div class="summary-value">{{ number_format($totalCalculatedArea, 2) }}</div>
                <div class="summary-label">
                    <i class="fas fa-chart-bar me-1"></i>Total Calculated Area
                </div>
            </div>
        </div>
    </div>

    <!-- Variation Summary Card -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="summary-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <div class="summary-icon">
                    <i class="fas fa-chart-line" style="color: #D4A13E;"></i>
                </div>
                <div class="summary-value {{ $totalAreaVariation >= 0 ? 'variation-positive' : 'variation-negative' }}">
                    {{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}
                </div>
                <div class="summary-label">
                    <i class="fas fa-percent me-1"></i>Total Area Variation
                </div>
                @php
                    $avgVariationPercentage = $totalMisPlotArea > 0 ? ($totalAreaVariation / $totalMisPlotArea) * 100 : 0;
                @endphp
                <div class="mt-2">
                    <span class="badge {{ $avgVariationPercentage >= 0 ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                        <i class="fas fa-percent me-1"></i>
                        {{ $avgVariationPercentage >= 0 ? '+' : '' }}{{ number_format($avgVariationPercentage, 2) }}% Average Variation
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Note -->
    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div>
                <strong>Understanding Variations:</strong>
                <ul class="mb-0 mt-1">
                    <li><strong>Calculated Area</strong> = (Sq. Feet × Floor Percentage / 100) × Number of Floors + (Sq. Feet × Basement)</li>
                    <li><strong>Area Variation</strong> = Calculated Area - MIS Plot Area (Positive = Under-assessment, Negative = Over-assessment)</li>
                    <li class="text-muted small mt-1"><i class="fas fa-lightbulb"></i> <strong>Note:</strong> Totals shown above are calculated from ALL buildings in this ward (not just current page)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="8%">S.No</th>
                        <th width="12%">GIS ID</th>
                        <th width="10%">Sq. Feet</th>
                        <th width="10%">Floors</th>
                        <th width="8%">%</th>
                        <th width="8%">Basement</th>
                        <th width="12%">MIS Plot Area</th>
                        <th width="12%">Calculated Area</th>
                        <th width="12%">Area Variation</th>
                        <th width="10%">Variation %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($result as $index => $row)
                    <tr>
                        <td>{{ $result->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-dark">
                                <i class="fas fa-map-pin me-1"></i>
                                {{ $row['gisid'] }}
                            </span>
                        </td>
                        <td>{{ number_format($row['sqfeet'], 2) }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $row['number_floor'] }} Floor(s)
                            </span>
                        </td>
                        <td>{{ number_format($row['percentage'], 2) }}%</td>
                        <td>
                            @if($row['basement'] > 0)
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ $row['basement'] }} Level(s)
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ number_format($row['mis_plot_area'], 2) }}</td>
                        <td>{{ number_format($row['calculated_area'], 2) }}</td>
                        <td class="{{ $row['area_variation'] >= 0 ? 'variation-positive' : 'variation-negative' }}">
                            <i class="fas {{ $row['area_variation'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                            {{ $row['area_variation'] >= 0 ? '+' : '' }}{{ number_format($row['area_variation'], 2) }}
                        </td>
                        <td class="{{ $row['variation_percentage'] >= 0 ? 'variation-positive' : 'variation-negative' }}">
                            {{ $row['variation_percentage'] >= 0 ? '+' : '' }}{{ number_format($row['variation_percentage'], 2) }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                            <p class="mb-0">No variation data found for this ward</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2"><strong>TOTAL</strong></td>
                        <td><strong>{{ number_format($totalSqfeet, 2) }}</strong></td>
                        <td colspan="2"></td>
                        <td></td>
                        <td><strong>{{ number_format($totalMisPlotArea, 2) }}</strong></td>
                        <td><strong>{{ number_format($totalCalculatedArea, 2) }}</strong></td>
                        <td>
                            <strong class="{{ $totalAreaVariation >= 0 ? 'text-warning' : 'text-danger' }}">
                                {{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}
                            </strong>
                        </td>
                        <td>
                            @php
                                $totalVariationPercent = $totalMisPlotArea > 0 ? ($totalAreaVariation / $totalMisPlotArea) * 100 : 0;
                            @endphp
                            <strong class="{{ $totalVariationPercent >= 0 ? 'text-warning' : 'text-danger' }}">
                                {{ $totalVariationPercent >= 0 ? '+' : '' }}{{ number_format($totalVariationPercent, 2) }}%
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="custom-pagination-wrapper mt-4">
        {{ $result->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

    <!-- Export Options -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <button onclick="exportToExcel()" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export to Excel
                </button>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        // Get the table data
        const table = document.querySelector('.table-custom');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });

        // Add summary info
        const summaryData = [
            ['WARD VARIATION REPORT'],
            ['Generated on:', new Date().toLocaleString()],
            ['Ward No:', '{{ $warddetail->ward_no }}'],
            ['Zone:', '{{ ucfirst($warddetail->zone) }}'],
            [''],
            ['SUMMARY STATISTICS'],
            ['Total Buildings:', '{{ number_format($totalBuildings) }}'],
            ['Total Sq. Feet:', '{{ number_format($totalSqfeet, 2) }}'],
            ['Total MIS Plot Area:', '{{ number_format($totalMisPlotArea, 2) }}'],
            ['Total Calculated Area:', '{{ number_format($totalCalculatedArea, 2) }}'],
            ['Total Area Variation:', '{{ $totalAreaVariation >= 0 ? "+" : "" }}{{ number_format($totalAreaVariation, 2) }}'],
            [''],
            ['DETAILED DATA']
        ];

        // Add summary to worksheet
        XLSX.utils.sheet_add_aoa(ws, summaryData, { origin: 'A1' });

        // Adjust column widths
        ws['!cols'] = [
            {wch:8}, {wch:12}, {wch:10}, {wch:10},
            {wch:8}, {wch:8}, {wch:12}, {wch:12},
            {wch:12}, {wch:10}
        ];

        XLSX.utils.book_append_sheet(wb, ws, 'Ward_{{ $warddetail->ward_no }}_Variations');
        XLSX.writeFile(wb, 'Ward_{{ $warddetail->ward_no }}_Building_Variations.xlsx');
    }

    // Print styling
    window.onbeforeprint = function() {
        document.querySelectorAll('.btn, .menu-toggle, .navbar-custom .dropdown').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelector('.sidebar').style.display = 'none';
        document.querySelector('.main-content').style.width = '100%';
        document.querySelector('.main-content').style.marginLeft = '0';
    };

    window.onafterprint = function() {
        location.reload();
    };

    // Tooltip for variation explanation
    document.querySelectorAll('.variation-positive, .variation-negative').forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            const value = parseFloat(this.innerText);
            if (value > 0) {
                this.title = 'Positive variation indicates under-assessment of property tax';
            } else if (value < 0) {
                this.title = 'Negative variation indicates over-assessment of property tax';
            }
        });
    });

    // Add animation to rows on load
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.table-custom tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            row.style.transition = `all 0.3s ease ${index * 0.05}s`;
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, 100);
        });
    });
</script>
@endsection
