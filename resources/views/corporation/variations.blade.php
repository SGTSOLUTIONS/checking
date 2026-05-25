{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Building Variations - Ward ' . ($warddetail->ward_no ?? ''))

@section('styles')
<style>
    /* Modern Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card-modern {
        background: var(--card-white);
        border-radius: 24px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(212, 161, 62, 0.1);
        box-shadow: var(--card-shadow);
    }

    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
        border-color: rgba(212, 161, 62, 0.3);
    }

    .stat-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #D4A13E, #E86A5F, #1A6B6E);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-icon-modern {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, rgba(212, 161, 62, 0.12), rgba(232, 106, 95, 0.12));
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
        letter-spacing: -0.02em;
    }

    .stat-label {
        color: var(--text-light);
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Variation Highlight Card */
    .variation-card {
        background: linear-gradient(135deg, #0B2B40 0%, #1A6B6E 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .variation-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(212, 161, 62, 0.1) 0%, transparent 70%);
        animation: pulse 8s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }

    .variation-content {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .variation-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 1rem;
    }

    .variation-amount {
        font-size: 3.5rem;
        font-weight: 800;
        margin: 1rem 0;
        letter-spacing: -0.02em;
    }

    .variation-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Table Styles */
    .data-table-wrapper {
        background: var(--card-white);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
    }

    .table-header {
        background: linear-gradient(135deg, #0B2B40 0%, #1A6B6E 100%);
        padding: 1.25rem 1.5rem;
    }

    .table-header h5 {
        color: white;
        margin: 0;
        font-weight: 600;
    }

    .modern-table {
        margin-bottom: 0;
    }

    .modern-table thead th {
        background: #F8F9FA;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1rem;
        border-bottom: 2px solid rgba(212, 161, 62, 0.2);
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(212, 161, 62, 0.03), rgba(232, 106, 95, 0.03));
        transform: scale(1.01);
    }

    .modern-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--text-dark);
    }

    .badge-modern {
        padding: 6px 12px;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .badge-gold {
        background: rgba(212, 161, 62, 0.15);
        color: #D4A13E;
    }

    .badge-teal {
        background: rgba(26, 107, 110, 0.15);
        color: #1A6B6E;
    }

    .badge-coral {
        background: rgba(232, 106, 95, 0.15);
        color: #E86A5F;
    }

    .variation-up {
        color: #10b981;
        font-weight: 600;
    }

    .variation-down {
        color: #ef4444;
        font-weight: 600;
    }

    /* Footer Total Row */
    .total-footer {
        background: linear-gradient(135deg, #F8F9FA, #FFFFFF);
        border-top: 2px solid rgba(212, 161, 62, 0.2);
        font-weight: 700;
    }

    .total-footer td {
        padding: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        border-top: 2px solid #D4A13E;
    }

    /* Ward Header */
    .ward-header-modern {
        background: linear-gradient(135deg, #0B2B40 0%, #1A6B6E 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .ward-header-modern::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='rgba(255,255,255,0.05)'%3E%3Cpath d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/%3E%3C/svg%3E");
        background-size: cover;
        opacity: 0.1;
    }

    /* Info Alert */
    .info-alert {
        background: linear-gradient(135deg, rgba(212, 161, 62, 0.08), rgba(232, 106, 95, 0.08));
        border-left: 4px solid #D4A13E;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 2rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    .btn-modern {
        padding: 0.6rem 1.25rem;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-modern-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-modern-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-modern-secondary {
        background: linear-gradient(135deg, #6B7A7F, #4B5A5F);
        color: white;
    }

    .btn-modern-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(107, 122, 127, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.5rem;
        }

        .variation-amount {
            font-size: 2rem;
        }

        .modern-table {
            font-size: 0.75rem;
        }

        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }

        .ward-header-modern {
            padding: 1.5rem;
        }
    }

    /* Print Styles */
    @media print {
        .action-buttons,
        .menu-toggle,
        .navbar-custom,
        .sidebar {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .ward-header-modern {
            background: #0B2B40 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .stat-card-modern,
        .variation-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@section('content')
<div class="content-panel">
    <!-- Modern Ward Header -->
    <div class="ward-header-modern">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="mb-3">
                    <a href="{{ route('corporation.dashboard') }}" class="text-white-50 text-decoration-none small">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                    <i class="fas fa-chevron-right mx-2 text-white-50 fa-xs"></i>
                    <span class="text-white">Ward {{ $warddetail->ward_no }}</span>
                </div>
                <h2 class="fw-bold text-white mb-2">
                    <i class="fas fa-building me-3"></i>
                    Building Variations Analysis
                </h2>
                <div class="d-flex gap-3 text-white-50 small">
                    <span><i class="fas fa-location-dot me-1"></i> Zone: {{ ucfirst($warddetail->zone) }}</span>
                    <span><i class="fas fa-calendar me-1"></i> {{ now()->format('d M, Y') }}</span>
                </div>
            </div>
            <div class="action-buttons">
                <button onclick="window.print()" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <button onclick="exportToExcel()" class="btn-modern btn-modern-success">
                    <i class="fas fa-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card-modern">
            <div class="stat-header">
                <div class="stat-icon-modern">
                    <i class="fas fa-building" style="color: #1A6B6E; font-size: 28px;"></i>
                </div>
                <i class="fas fa-chart-line text-muted"></i>
            </div>
            <div class="stat-value">{{ number_format($totalBuildings) }}</div>
            <div class="stat-label">Total Buildings</div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-header">
                <div class="stat-icon-modern">
                    <i class="fas fa-vector-square" style="color: #D4A13E; font-size: 28px;"></i>
                </div>
                <i class="fas fa-ruler text-muted"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSqfeet, 2) }}</div>
            <div class="stat-label">Total Sq. Feet</div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-header">
                <div class="stat-icon-modern">
                    <i class="fas fa-database" style="color: #E86A5F; font-size: 28px;"></i>
                </div>
                <i class="fas fa-chart-bar text-muted"></i>
            </div>
            <div class="stat-value">{{ number_format($totalMisPlotArea, 2) }}</div>
            <div class="stat-label">MIS Plot Area</div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-header">
                <div class="stat-icon-modern">
                    <i class="fas fa-calculator" style="color: #0B2B40; font-size: 28px;"></i>
                </div>
                <i class="fas fa-chart-pie text-muted"></i>
            </div>
            <div class="stat-value">{{ number_format($totalCalculatedArea, 2) }}</div>
            <div class="stat-label">Calculated Area</div>
        </div>
    </div>

    <!-- Variation Highlight Card -->
    <div class="variation-card">
        <div class="variation-content">
            <div class="variation-label">
                <i class="fas fa-chart-line me-2"></i>Total Area Variation
            </div>
            <div class="variation-amount {{ $totalAreaVariation >= 0 ? 'variation-up' : 'variation-down' }}">
                {{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}
            </div>
            @php
                $avgVariationPercentage = $totalMisPlotArea > 0 ? ($totalAreaVariation / $totalMisPlotArea) * 100 : 0;
            @endphp
            <div>
                <span class="variation-badge {{ $avgVariationPercentage >= 0 ? 'badge-teal' : 'badge-coral' }}"
                      style="background: {{ $avgVariationPercentage >= 0 ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }};
                             color: {{ $avgVariationPercentage >= 0 ? '#10b981' : '#ef4444' }};">
                    <i class="fas fa-percent me-1"></i>
                    {{ $avgVariationPercentage >= 0 ? '+' : '' }}{{ number_format($avgVariationPercentage, 2) }}% Average
                </span>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="info-alert">
        <div class="d-flex gap-3">
            <div>
                <i class="fas fa-lightbulb" style="color: #D4A13E; font-size: 1.5rem;"></i>
            </div>
            <div>
                <strong class="d-block mb-1">Understanding the Calculations</strong>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            <i class="fas fa-formula me-1"></i>
                            <strong>Calculated Area</strong> = (Sq. Feet × Floor % / 100) × Floors + (Sq. Feet × Basement)
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            <i class="fas fa-chart-line me-1"></i>
                            <strong>Area Variation</strong> = Calculated Area - MIS Area
                            <span class="badge-modern badge-gold ms-2">+ = Under-assessment</span>
                            <span class="badge-modern badge-coral ms-1">- = Over-assessment</span>
                        </small>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Note:</strong> All totals reflect ALL buildings in this ward, not just the current page.
                </small>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table-wrapper">
        <div class="table-header">
            <h5>
                <i class="fas fa-table me-2"></i>
                Detailed Building Data
                <span class="badge bg-white text-dark ms-2">{{ $result->total() }} Records</span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="modern-table table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>GIS ID</th>
                        <th>Sq. Feet</th>
                        <th>Floors</th>
                        <th>Floor %</th>
                        <th>Basement</th>
                        <th>MIS Area</th>
                        <th>Calculated</th>
                        <th>Variation</th>
                        <th>Variation %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($result as $index => $row)
                    <tr>
                        <td>{{ $result->firstItem() + $index }}</td>
                        <td>
                            <span class="badge-modern badge-gold">
                                <i class="fas fa-map-pin me-1"></i>
                                {{ $row['gisid'] }}
                            </span>
                        </td>
                        <td class="fw-semibold">{{ number_format($row['sqfeet'], 2) }}</td>
                        <td>
                            <span class="badge-modern badge-teal">
                                <i class="fas fa-layer-group me-1"></i>
                                {{ $row['number_floor'] }}
                            </span>
                        </td>
                        <td>{{ number_format($row['percentage'], 1) }}%</td>
                        <td>
                            @if($row['basement'] > 0)
                                <span class="badge-modern badge-coral">
                                    <i class="fas fa-arrow-down me-1"></i>
                                    {{ $row['basement'] }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ number_format($row['mis_plot_area'], 2) }}</td>
                        <td>{{ number_format($row['calculated_area'], 2) }}</td>
                        <td class="{{ $row['area_variation'] >= 0 ? 'variation-up' : 'variation-down' }}">
                            <i class="fas {{ $row['area_variation'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                            {{ $row['area_variation'] >= 0 ? '+' : '' }}{{ number_format($row['area_variation'], 2) }}
                        </td>
                        <td class="{{ $row['variation_percentage'] >= 0 ? 'variation-up' : 'variation-down' }}">
                            {{ $row['variation_percentage'] >= 0 ? '+' : '' }}{{ number_format($row['variation_percentage'], 2) }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                            <p class="mb-0 text-muted">No variation data found for this ward</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="total-footer">
                    <tr>
                        <td colspan="2"><strong>Totals</strong></td>
                        <td><strong>{{ number_format($totalSqfeet, 2) }}</strong></td>
                        <td colspan="3"></td>
                        <td><strong>{{ number_format($totalMisPlotArea, 2) }}</strong></td>
                        <td><strong>{{ number_format($totalCalculatedArea, 2) }}</strong></td>
                        <td class="{{ $totalAreaVariation >= 0 ? 'variation-up' : 'variation-down' }}">
                            <strong>{{ $totalAreaVariation >= 0 ? '+' : '' }}{{ number_format($totalAreaVariation, 2) }}</strong>
                        </td>
                        <td class="{{ $totalVariationPercent >= 0 ? 'variation-up' : 'variation-down' }}">
                            <strong>{{ $totalVariationPercent >= 0 ? '+' : '' }}{{ number_format($totalVariationPercent, 2) }}%</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="custom-pagination-wrapper">
        {{ $result->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        const table = document.querySelector('.modern-table');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });

        const summaryData = [
            ['BUILDING VARIATION REPORT'],
            ['Generated:', new Date().toLocaleString()],
            ['Ward No:', '{{ $warddetail->ward_no }}'],
            ['Zone:', '{{ ucfirst($warddetail->zone) }}'],
            [''],
            ['SUMMARY STATISTICS'],
            ['Total Buildings:', '{{ number_format($totalBuildings) }}'],
            ['Total Sq. Feet:', '{{ number_format($totalSqfeet, 2) }}'],
            ['Total MIS Plot Area:', '{{ number_format($totalMisPlotArea, 2) }}'],
            ['Total Calculated Area:', '{{ number_format($totalCalculatedArea, 2) }}'],
            ['Total Area Variation:', '{{ $totalAreaVariation >= 0 ? "+" : "" }}{{ number_format($totalAreaVariation, 2) }}'],
            ['Average Variation %:', '{{ $totalVariationPercent >= 0 ? "+" : "" }}{{ number_format($totalVariationPercent, 2) }}%'],
            [''],
            ['DETAILED DATA']
        ];

        XLSX.utils.sheet_add_aoa(ws, summaryData, { origin: 'A1' });

        ws['!cols'] = [
            {wch:8}, {wch:14}, {wch:12}, {wch:8},
            {wch:8}, {wch:8}, {wch:12}, {wch:12},
            {wch:12}, {wch:10}
        ];

        XLSX.utils.book_append_sheet(wb, ws, 'Ward_{{ $warddetail->ward_no }}');
        XLSX.writeFile(wb, 'Ward_{{ $warddetail->ward_no }}_Building_Variations.xlsx');
    }

    // Smooth row animations
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.modern-table tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            row.style.transition = `all 0.3s ease ${index * 0.05}s`;
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 100);
        });
    });
</script>
@endsection
