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
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($paginatedResult->total()) }}</h2>
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
                                {{ number_format(collect($paginatedResult->items())->sum('mis_plot_area')) }} <small>sq.ft</small>
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
                                {{ number_format(collect($paginatedResult->items())->sum('calculated_area')) }} <small>sq.ft</small>
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
                                $totalVariation = collect($paginatedResult->items())->sum('area_variation');
                                $variationClass = $totalVariation >= 0 ? 'text-success' : 'text-danger';
                                $variationIcon = $totalVariation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            @endphp
                            <h2 class="fw-bold mb-0 {{ $variationClass }}">
                                {{ number_format($totalVariation) }} <small>sq.ft</small>
                            </h2>
                            <small class="{{ $variationClass }}">
                                <i class="fas {{ $variationIcon }}"></i>
                                {{ number_format(($totalVariation / max(collect($paginatedResult->items())->sum('mis_plot_area'), 1)) * 100, 2) }}% variation
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
                                    Total Buildings: {{ number_format($paginatedResult->total()) }}
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
                                        <th>GIS ID</th>
                                        <th>Polygon Area (sq.ft)</th>
                                        <th>Floors</th>
                                        <th>Floor %</th>
                                        <th>Basement</th>
                                        <th>Calculated Area (sq.ft)</th>
                                        <th>MIS Plot Area (sq.ft)</th>
                                        <th>Area Variation (sq.ft)</th>
                                        <th>Variation %</th>
                                        <th>Assessments</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Data will be loaded from paginatedResult -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                            <div class="mb-2 mb-sm-0">
                                <small class="text-muted">
                                    Showing {{ $paginatedResult->firstItem() ?? 0 }} to {{ $paginatedResult->lastItem() ?? 0 }}
                                    of {{ $paginatedResult->total() ?? 0 }} buildings
                                </small>
                            </div>
                            <div>
                                {{ $paginatedResult->appends(request()->query())->links() }}
                            </div>
                        </div>

                        <!-- No Data Message -->
                        <div id="noDataMessage" class="alert alert-info text-center py-5 d-none">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <h5>No data available</h5>
                            <p class="mb-0">No buildings match your filter criteria.</p>
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
            // Data from server (current page only)
            const variationsData = @json($paginatedResult->items());

            // Render initial table
            renderTable(variationsData);
            updateResultCount(variationsData.length);

            function renderTable(data) {
                const $tbody = $("#tableBody");
                $tbody.empty();

                if (data.length === 0) {
                    $("#noDataMessage").removeClass('d-none');
                    $("#variationTable").addClass('d-none');
                    return;
                }

                $("#noDataMessage").addClass('d-none');
                $("#variationTable").removeClass('d-none');

                data.forEach(function(item, index) {
                    let statusBadge = '';
                    let variationClass = '';

                    if (item.variation_percentage > 0) {
                        statusBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-arrow-up me-1"></i>Extra Area</span>';
                        variationClass = 'table-warning';
                    } else if (item.variation_percentage < 0) {
                        statusBadge = '<span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i>Less Area</span>';
                        variationClass = 'table-danger';
                    } else {
                        statusBadge = '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Matched</span>';
                        variationClass = 'table-success';
                    }

                    let row = `
                        <tr class="${variationClass}">
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
                            <td class="${item.variation_percentage > 0 ? 'text-warning' : (item.variation_percentage < 0 ? 'text-danger' : 'text-success')} fw-bold">
                                ${item.variation_percentage > 0 ? '+' : ''}${item.variation_percentage}%
                            </td>
                            <td><span class="badge bg-secondary">${item.assessment_count}</span></td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                    $tbody.append(row);
                });
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
                return str.replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            function updateResultCount(count) {
                $("#resultCount").text(count);
            }

            function applyFilters() {
                var filterGisid = $("#filterGisid").val().toLowerCase().trim();
                var filterVariationType = $("#filterVariationType").val();
                var filterMinPercentage = parseFloat($("#filterMinPercentage").val());

                var filteredData = variationsData.filter(function(item) {
                    var gisidMatch = filterGisid === "" || item.gisid.toLowerCase().includes(filterGisid);
                    var typeMatch = filterVariationType === "all" ||
                        (filterVariationType === "positive" && item.variation_percentage > 0) ||
                        (filterVariationType === "negative" && item.variation_percentage < 0) ||
                        (filterVariationType === "zero" && item.variation_percentage === 0);
                    var percentageMatch = isNaN(filterMinPercentage) || filterMinPercentage <= 0 ||
                        Math.abs(item.variation_percentage) >= filterMinPercentage;
                    return gisidMatch && typeMatch && percentageMatch;
                });

                renderTable(filteredData);
                updateResultCount(filteredData.length);
            }

            function debounce(func, delay) {
                let timeout;
                return function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, arguments), delay);
                };
            }

            const debouncedApplyFilters = debounce(applyFilters, 300);

            $("#filterGisid").on("keyup", debouncedApplyFilters);
            $("#filterVariationType").on("change", applyFilters);
            $("#filterMinPercentage").on("keyup", debouncedApplyFilters);

            // Copy GIS ID functionality
            $(document).on('click', '.copy-gisid', function(e) {
                e.preventDefault();
                const gisid = $(this).data('gisid');

                navigator.clipboard.writeText(gisid).then(function() {
                    const $btn = $(e.currentTarget);
                    const originalIcon = $btn.html();
                    $btn.html('<i class="fas fa-check text-success"></i>');
                    setTimeout(() => $btn.html(originalIcon), 1000);
                }).catch(function() {
                    const tempInput = $('<input>');
                    $('body').append(tempInput);
                    tempInput.val(gisid).select();
                    document.execCommand('copy');
                    tempInput.remove();
                    const $btn = $(e.currentTarget);
                    const originalIcon = $btn.html();
                    $btn.html('<i class="fas fa-check text-success"></i>');
                    setTimeout(() => $btn.html(originalIcon), 1000);
                });
            });

            // Export current page data
            $("#exportBtn").on("click", function() {
                var filterGisid = $("#filterGisid").val().toLowerCase().trim();
                var filterVariationType = $("#filterVariationType").val();
                var filterMinPercentage = parseFloat($("#filterMinPercentage").val());

                var exportData = variationsData.filter(function(item) {
                    var gisidMatch = filterGisid === "" || item.gisid.toLowerCase().includes(filterGisid);
                    var typeMatch = filterVariationType === "all" ||
                        (filterVariationType === "positive" && item.variation_percentage > 0) ||
                        (filterVariationType === "negative" && item.variation_percentage < 0) ||
                        (filterVariationType === "zero" && item.variation_percentage === 0);
                    var percentageMatch = isNaN(filterMinPercentage) || filterMinPercentage <= 0 ||
                        Math.abs(item.variation_percentage) >= filterMinPercentage;
                    return gisidMatch && typeMatch && percentageMatch;
                });

                const headers = [
                    'GIS ID', 'Polygon Area (sq.ft)', 'Number of Floors', 'Floor Percentage',
                    'Basement', 'Calculated Area (sq.ft)', 'MIS Plot Area (sq.ft)',
                    'Area Variation (sq.ft)', 'Variation Percentage (%)', 'Assessment Count'
                ];

                const csvRows = [headers.join(',')];
                exportData.forEach(item => {
                    const row = [
                        `"${item.gisid}"`, item.sqfeet, item.number_floor, item.percentage,
                        item.basement || 0, item.calculated_area, item.mis_plot_area,
                        item.area_variation, item.variation_percentage, item.assessment_count
                    ];
                    csvRows.push(row.join(','));
                });

                const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `variation_page_${{{ $paginatedResult->currentPage() }}}_ward_${@json($warddetail->ward_no ?? '')}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            });
        });
    </script>

    <!-- Keep all your existing CSS styles here -->
    <style>
        /* Your existing CSS styles - keep them exactly as they are */
        .dashboard-content-area {
            padding: 20px;
            background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
            min-height: 100vh;
        }
        /* ... rest of your existing styles ... */

        /* Add pagination styles */
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
    </style>
@endpush
