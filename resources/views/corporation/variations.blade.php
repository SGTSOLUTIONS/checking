{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Variation Analysis - ' . ($warddetail->zone ?? '') . ' Ward ' . ($warddetail->ward_no ?? ''))

@section('content')

    <div class="dashboard-content-area">

        <div class="animate__animated animate__fadeInUp">

            <!-- Header with Back Navigation -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-light mb-2">
                        <i class="fas fa-arrow-left me-2"></i> Back to Analytics
                    </a>
                    <h3 class="fw-bold text-white mt-2">
                        <i class="fas fa-chart-line me-2" style="color:#1679AB;"></i>
                        Variation Analysis - {{ $warddetail->zone ?? '' }} Zone, Ward {{ $warddetail->ward_no ?? '' }}
                    </h3>
                </div>
                <div>
                    <span class="badge bg-light text-dark p-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ now()->format('d M Y') }}
                    </span>
                </div>
            </div>

            <!-- Summary Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Buildings (GIS)</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($polygons->total()) }}</h2>
                            <small class="text-primary"><i class="fas fa-draw-polygon"></i> Polygons analyzed</small>
                        </div>
                        <div class="stat-icon"><i class="fas fa-building"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total MIS Area</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">
                                {{ number_format($totalMisAreaAll) }} <small>sq.ft</small>
                            </h2>
                            <small class="text-info"><i class="fas fa-database"></i> From MIS records</small>
                        </div>
                        <div class="stat-icon bg-info-subtle"><i class="fas fa-chart-line text-info"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Calculated Area</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">
                                {{ number_format($totalCalculatedAreaAll) }} <small>sq.ft</small>
                            </h2>
                            <small class="text-warning"><i class="fas fa-calculator"></i> Based on building parameters</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle"><i class="fas fa-ruler-combined text-warning"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Area Variation</h6>
                            @php
                                $variationClass = $totalVariationAll >= 0 ? 'text-success' : 'text-danger';
                                $variationIcon = $totalVariationAll >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            @endphp
                            <h2 class="fw-bold mb-0 {{ $variationClass }}">
                                {{ number_format($totalVariationAll) }} <small>sq.ft</small>
                            </h2>
                            <small class="{{ $variationClass }}">
                                <i class="fas {{ $variationIcon }}"></i>
                                {{ number_format(abs($totalVariationPercentageAll), 2) }}% variation
                            </small>
                        </div>
                        <div class="stat-icon bg-danger-subtle"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                    </div>
                </div>
            </div>

            <!-- Ward Information Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="fas fa-map-marked-alt me-2" style="color:#1679AB;"></i>
                                    Ward Information
                                </h5>
                                <p class="text-muted mb-0">
                                    Zone: <strong>{{ $warddetail->zone ?? 'N/A' }}</strong> |
                                    Ward Number: <strong>{{ $warddetail->ward_no ?? 'N/A' }}</strong> |
                                    Corporation ID: <strong>{{ $warddetail->corporation_id ?? 'N/A' }}</strong>
                                </p>
                            </div>
                            <div class="mt-2 mt-sm-0">
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Total Buildings: {{ number_format($polygons->total()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variation Details Table -->
            <div class="row">
                <div class="col-12">
                    <div class="stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                            <h4 class="fw-bold mb-0">
                                <i class="fas fa-chart-line me-2" style="color:#1679AB;"></i>
                                Building-wise Variation Details
                            </h4>
                            <div>
                                <button class="btn btn-sm btn-outline-primary" id="exportBtn">
                                    <i class="fas fa-download me-1"></i> Export Current Page
                                </button>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="text-dark fw-bold mb-2">Filter by GIS ID:</label>
                                <input type="text" class="form-control" id="filterGisid" placeholder="Enter GIS ID...">
                            </div>
                            <div class="col-md-4">
                                <label class="text-dark fw-bold mb-2">Filter by Variation Type:</label>
                                <select class="form-select" id="filterVariationType">
                                    <option value="all">All Variations</option>
                                    <option value="positive">Positive Variation (Extra Area)</option>
                                    <option value="negative">Negative Variation (Less Area)</option>
                                    <option value="zero">No Variation</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="text-dark fw-bold mb-2">Min Variation Percentage:</label>
                                <input type="number" class="form-control" id="filterMinPercentage" placeholder="e.g., 10">
                                <small class="text-muted">Show variations greater than this %</small>
                            </div>
                        </div>

                        <!-- Results Counter -->
                        <div class="mb-3">
                            <span class="badge bg-primary p-2">
                                <i class="fas fa-chart-bar me-1"></i>
                                Showing <span id="resultCount">0</span> buildings
                            </span>
                        </div>

                        <!-- Table Container -->
                        <div class="table-responsive">
                            <table class="table table-hover variation-table" id="variationTable">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">GIS ID</th>
                                        <th style="width: 10%">Polygon Area (sq.ft)</th>
                                        <th style="width: 8%">Floors</th>
                                        <th style="width: 8%">Floor %</th>
                                        <th style="width: 8%">Basement</th>
                                        <th style="width: 12%">Calculated Area (sq.ft)</th>
                                        <th style="width: 12%">MIS Plot Area (sq.ft)</th>
                                        <th style="width: 12%">Area Variation (sq.ft)</th>
                                        <th style="width: 8%">Variation %</th>
                                        <th style="width: 7%">Assessments</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @forelse($polygons as $item)
                                    @php
                                        $rowClass = $item->variation_percentage > 0 ? 'table-warning' : ($item->variation_percentage < 0 ? 'table-danger' : 'table-success');
                                    @endphp
                                    <tr class="{{ $rowClass }}" data-gisid="{{ $item->gisid }}" data-variation="{{ $item->variation_percentage }}">
                                        <td>
                                            <strong>{{ $item->gisid }}</strong>
                                            <button class="btn btn-sm btn-link p-0 ms-2 copy-gisid" data-gisid="{{ $item->gisid }}">
                                                <i class="fas fa-copy text-muted"></i>
                                            </button>
                                         </td>
                                        <td>{{ number_format($item->sqfeet, 2) }}</td>
                                        <td>{{ $item->number_floor }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->basement > 0 ? $item->basement : '-' }}</td>
                                        <td class="fw-bold">{{ number_format($item->calculated_area, 2) }}</td>
                                        <td>{{ number_format($item->mis_plot_area, 2) }}</td>
                                        <td class="{{ $item->area_variation > 0 ? 'text-success' : ($item->area_variation < 0 ? 'text-danger' : '') }}">
                                            {{ $item->area_variation > 0 ? '+' : '' }}{{ number_format($item->area_variation, 2) }}
                                         </td>
                                        <td class="{{ $item->variation_percentage > 0 ? 'text-warning' : ($item->variation_percentage < 0 ? 'text-danger' : 'text-success') }} fw-bold">
                                            {{ $item->variation_percentage > 0 ? '+' : '' }}{{ number_format($item->variation_percentage, 2) }}%
                                         </td>
                                        <td><span class="badge bg-secondary">{{ $item->assessment_count }}</span></td>
                                     </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                No variation data found for this ward
                                            </div>
                                         </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                             </table>
                        </div>

                        <!-- Stylish Pagination -->
                        <div class="pagination-wrapper mt-4">
                            @if($polygons->hasPages())
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                                    <div class="pagination-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Showing
                                        <strong>{{ $polygons->firstItem() ?? 0 }}</strong>
                                        to
                                        <strong>{{ $polygons->lastItem() ?? 0 }}</strong>
                                        of
                                        <strong>{{ $polygons->total() }}</strong>
                                        buildings
                                    </div>

                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($polygons->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                        <span class="d-none d-sm-inline"> Previous</span>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $polygons->previousPageUrl() }}" rel="prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                        <span class="d-none d-sm-inline"> Previous</span>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @php
                                                $start = max(1, $polygons->currentPage() - 2);
                                                $end = min($start + 4, $polygons->lastPage());
                                                if ($end - $start < 4 && $polygons->lastPage() > 5) {
                                                    $start = max(1, $polygons->lastPage() - 4);
                                                    $end = $polygons->lastPage();
                                                }
                                            @endphp

                                            {{-- First Page --}}
                                            @if($start > 1)
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $polygons->url(1) }}">1</a>
                                                </li>
                                                @if($start > 2)
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                            @endif

                                            {{-- Page Numbers --}}
                                            @for($i = $start; $i <= $end; $i++)
                                                @if($i == $polygons->currentPage())
                                                    <li class="page-item active" aria-current="page">
                                                        <span class="page-link">{{ $i }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $polygons->url($i) }}">{{ $i }}</a>
                                                    </li>
                                                @endif
                                            @endfor

                                            {{-- Last Page --}}
                                            @if($end < $polygons->lastPage())
                                                @if($end < $polygons->lastPage() - 1)
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $polygons->url($polygons->lastPage()) }}">
                                                        {{ $polygons->lastPage() }}
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Next Page Link --}}
                                            @if ($polygons->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $polygons->nextPageUrl() }}" rel="next">
                                                        <span class="d-none d-sm-inline">Next </span>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <span class="d-none d-sm-inline">Next </span>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>

                                    {{-- Per Page Selector --}}
                                    <div class="per-page-selector">
                                        <label class="text-muted me-2 mb-0">
                                            <i class="fas fa-eye"></i>
                                            <span class="d-none d-sm-inline">Show:</span>
                                        </label>
                                        <select class="form-select form-select-sm" id="perPageSelect" style="width: auto; display: inline-block;">
                                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                        <span class="text-muted d-none d-sm-inline">per page</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Get all data items from the current page
    const variationsData = @json($polygons->items());

    // Function to render filtered table
    function renderFilteredTable(data) {
        const $tbody = $("#tableBody");

        if (data.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No buildings match your filter criteria
                        </div>
                     </td>
                </tr>
            `);
            $("#resultCount").text(0);
            return;
        }

        let html = '';
        data.forEach(function(item) {
            let rowClass = '';
            let variationClass = '';

            if (item.variation_percentage > 0) {
                rowClass = 'table-warning';
                variationClass = 'text-warning';
            } else if (item.variation_percentage < 0) {
                rowClass = 'table-danger';
                variationClass = 'text-danger';
            } else {
                rowClass = 'table-success';
                variationClass = 'text-success';
            }

            html += `
                <tr class="${rowClass}" data-gisid="${escapeHtml(item.gisid)}" data-variation="${item.variation_percentage}">
                    <td>
                        <strong>${escapeHtml(item.gisid)}</strong>
                        <button class="btn btn-sm btn-link p-0 ms-2 copy-gisid" data-gisid="${escapeHtml(item.gisid)}">
                            <i class="fas fa-copy text-muted"></i>
                        </button>
                     </td>
                    <td>${formatNumber(item.sqfeet)}</td>
                    <td>${item.number_floor}</td>
                    <td>${item.percentage}%</td>
                    <td>${item.basement > 0 ? item.basement : '-'}</td>
                    <td class="fw-bold">${formatNumber(item.calculated_area)}</td>
                    <td>${formatNumber(item.mis_plot_area)}</td>
                    <td class="${item.area_variation > 0 ? 'text-success' : (item.area_variation < 0 ? 'text-danger' : '')}">
                        ${item.area_variation > 0 ? '+' : ''}${formatNumber(item.area_variation)}
                     </td>
                    <td class="${variationClass} fw-bold">
                        ${item.variation_percentage > 0 ? '+' : ''}${item.variation_percentage}%
                     </td>
                    <td><span class="badge bg-secondary">${item.assessment_count}</span></td>
                 </tr>
            `;
        });

        $tbody.html(html);
        $("#resultCount").text(data.length);
    }

    function formatNumber(num) {
        if (num === null || num === undefined) return '0';
        return parseFloat(num).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function applyFilters() {
        var filterGisid = $("#filterGisid").val().toLowerCase().trim();
        var filterVariationType = $("#filterVariationType").val();
        var filterMinPercentage = parseFloat($("#filterMinPercentage").val());

        var filteredData = variationsData.filter(function(item) {
            var gisidMatch = filterGisid === "" || String(item.gisid).toLowerCase().includes(filterGisid);
            var typeMatch = filterVariationType === "all" ||
                (filterVariationType === "positive" && item.variation_percentage > 0) ||
                (filterVariationType === "negative" && item.variation_percentage < 0) ||
                (filterVariationType === "zero" && item.variation_percentage === 0);
            var percentageMatch = isNaN(filterMinPercentage) || filterMinPercentage <= 0 ||
                Math.abs(item.variation_percentage) >= filterMinPercentage;
            return gisidMatch && typeMatch && percentageMatch;
        });

        renderFilteredTable(filteredData);
    }

    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, arguments), delay);
        };
    }

    const debouncedApplyFilters = debounce(applyFilters, 300);

    // Event listeners
    $("#filterGisid").on("keyup", debouncedApplyFilters);
    $("#filterVariationType").on("change", applyFilters);
    $("#filterMinPercentage").on("keyup", debouncedApplyFilters);

    // Per page selector
    $("#perPageSelect").on("change", function() {
        var perPage = $(this).val();
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('per_page', perPage);
        currentUrl.searchParams.set('page', 1);
        window.location.href = currentUrl.toString();
    });

    // Copy GIS ID functionality
    $(document).on('click', '.copy-gisid', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const gisid = $(this).data('gisid');

        // Modern clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(String(gisid)).then(function() {
                showCopySuccess($(e.currentTarget));
            }).catch(function() {
                fallbackCopy(gisid, $(e.currentTarget));
            });
        } else {
            fallbackCopy(gisid, $(e.currentTarget));
        }
    });

    function fallbackCopy(text, $btn) {
        const tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(text).select();
        document.execCommand('copy');
        tempInput.remove();
        showCopySuccess($btn);
    }

    function showCopySuccess($btn) {
        const originalIcon = $btn.html();
        $btn.html('<i class="fas fa-check text-success"></i>');
        setTimeout(() => $btn.html(originalIcon), 1000);
    }

    // Export current filtered data
    $("#exportBtn").on("click", function() {
        var filterGisid = $("#filterGisid").val().toLowerCase().trim();
        var filterVariationType = $("#filterVariationType").val();
        var filterMinPercentage = parseFloat($("#filterMinPercentage").val());

        var exportData = variationsData.filter(function(item) {
            var gisidMatch = filterGisid === "" || String(item.gisid).toLowerCase().includes(filterGisid);
            var typeMatch = filterVariationType === "all" ||
                (filterVariationType === "positive" && item.variation_percentage > 0) ||
                (filterVariationType === "negative" && item.variation_percentage < 0) ||
                (filterVariationType === "zero" && item.variation_percentage === 0);
            var percentageMatch = isNaN(filterMinPercentage) || filterMinPercentage <= 0 ||
                Math.abs(item.variation_percentage) >= filterMinPercentage;
            return gisidMatch && typeMatch && percentageMatch;
        });

        if (exportData.length === 0) {
            alert("No data to export!");
            return;
        }

        const headers = [
            'GIS ID', 'Polygon Area (sq.ft)', 'Number of Floors', 'Floor Percentage',
            'Basement', 'Calculated Area (sq.ft)', 'MIS Plot Area (sq.ft)',
            'Area Variation (sq.ft)', 'Variation Percentage (%)', 'Assessment Count'
        ];

        const csvRows = [headers.join(',')];
        exportData.forEach(item => {
            const row = [
                `"${String(item.gisid).replace(/"/g, '""')}"`,
                item.sqfeet,
                item.number_floor,
                item.percentage,
                item.basement || 0,
                item.calculated_area,
                item.mis_plot_area,
                item.area_variation,
                item.variation_percentage,
                item.assessment_count
            ];
            csvRows.push(row.join(','));
        });

        const blob = new Blob(["\uFEFF" + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `variation_ward_{{ $warddetail->ward_no }}_page_{{ $polygons->currentPage() }}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });

    // Initialize result count
    $("#resultCount").text(variationsData.length);
});
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

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #1679AB;
        box-shadow: 0 0 0 0.2rem rgba(22, 121, 171, 0.25);
    }

    .variation-table {
        border-radius: 12px;
        overflow: hidden;
    }

    .variation-table thead th {
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        color: white;
        font-weight: 600;
        border: none;
        padding: 12px 10px;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .variation-table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .variation-table tbody tr {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .variation-table tbody tr:hover {
        background-color: rgba(22, 121, 171, 0.1);
        transform: scale(1.01);
    }

    .badge {
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 500;
        border-radius: 20px;
    }

    .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .btn-outline-light:hover {
        background: white;
        color: #102C57;
        border-color: white;
    }

    .btn-outline-primary {
        border-color: #1679AB;
        color: #1679AB;
    }

    .btn-outline-primary:hover {
        background: #1679AB;
        border-color: #1679AB;
        color: white;
    }

    .copy-gisid {
        opacity: 0.6;
        transition: opacity 0.2s ease;
    }

    .copy-gisid:hover {
        opacity: 1;
    }

    /* Stylish Pagination */
    .pagination-wrapper {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }

    .pagination-info {
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 10px;
        font-size: 14px;
        color: #102C57;
    }

    .pagination {
        gap: 5px;
        margin-bottom: 0;
    }

    .page-link {
        color: #102C57;
        border-radius: 10px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        padding: 8px 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .page-link:hover {
        background-color: #1679AB;
        border-color: #1679AB;
        color: white;
        transform: translateY(-2px);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        border-color: #1679AB;
        color: white;
        box-shadow: 0 2px 8px rgba(22, 121, 171, 0.3);
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        opacity: 0.6;
    }

    .per-page-selector {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        padding: 5px 12px;
        border-radius: 10px;
    }

    .per-page-selector select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 5px 10px;
        cursor: pointer;
    }

    .per-page-selector select:focus {
        border-color: #1679AB;
        outline: none;
        box-shadow: 0 0 0 2px rgba(22, 121, 171, 0.2);
    }

    .table-warning {
        background-color: #fff3cd !important;
    }

    .table-danger {
        background-color: #f8d7da !important;
    }

    .table-success {
        background-color: #d4edda !important;
    }

    @media (max-width: 768px) {
        .dashboard-content-area {
            padding: 15px;
        }

        .pagination-wrapper {
            padding: 15px;
        }

        .pagination-info {
            font-size: 12px;
            text-align: center;
            width: 100%;
        }

        .page-link {
            padding: 6px 10px;
            font-size: 12px;
        }

        .per-page-selector {
            font-size: 12px;
        }

        .variation-table thead th,
        .variation-table tbody td {
            font-size: 11px;
            padding: 8px 5px;
        }
    }
</style>
@endpush
