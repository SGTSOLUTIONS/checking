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
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format(count($result)) }}</h2>
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
                                {{ number_format(collect($result)->sum('mis_plot_area')) }} <small>sq.ft</small>
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
                                {{ number_format(collect($result)->sum('calculated_area')) }} <small>sq.ft</small>
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
                                $totalVariation = collect($result)->sum('area_variation');
                                $variationClass = $totalVariation >= 0 ? 'text-success' : 'text-danger';
                                $variationIcon = $totalVariation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            @endphp
                            <h2 class="fw-bold mb-0 {{ $variationClass }}">
                                {{ number_format($totalVariation) }} <small>sq.ft</small>
                            </h2>
                            <small class="{{ $variationClass }}">
                                <i class="fas {{ $variationIcon }}"></i>
                                {{ number_format(($totalVariation / max(collect($result)->sum('mis_plot_area'), 1)) * 100, 2) }}% variation
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
                                    Total Buildings: {{ count($result) }}
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
                                    <i class="fas fa-download me-1"></i> Export Data
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
                                    <!-- Data will be loaded by JavaScript -->
                                </tbody>
                            </table>
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
            // Data from server
            const variationsData = @json($result);

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
                    // Determine status and class
                    let statusBadge = '';
                    let statusClass = '';
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

                    // Format numbers
                    let row = `
                        <tr class="${variationClass}" data-gisid="${item.gisid.toLowerCase()}"
                            data-variation-type="${item.variation_percentage > 0 ? 'positive' : (item.variation_percentage < 0 ? 'negative' : 'zero')}"
                            data-variation-percentage="${Math.abs(item.variation_percentage)}">
                            <td>
                                <strong>${item.gisid}</strong>
                                <button class="btn btn-sm btn-link p-0 ms-2 copy-gisid" data-gisid="${item.gisid}">
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
                            <td>
                                <span class="badge bg-secondary">${item.assessment_count}</span>
                            </td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                    $tbody.append(row);
                });

                // Add animation to rows
                $('#tableBody tr').each(function(i) {
                    $(this).css('animation-delay', (i * 0.02) + 's');
                });
            }

            function formatNumber(num) {
                if (num === null || num === undefined) return '0';
                return parseFloat(num).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
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
                    // GIS ID filter
                    var gisidMatch = true;
                    if (filterGisid !== "") {
                        gisidMatch = item.gisid.toLowerCase().includes(filterGisid);
                    }

                    // Variation type filter
                    var typeMatch = true;
                    if (filterVariationType !== "all") {
                        if (filterVariationType === "positive") {
                            typeMatch = item.variation_percentage > 0;
                        } else if (filterVariationType === "negative") {
                            typeMatch = item.variation_percentage < 0;
                        } else if (filterVariationType === "zero") {
                            typeMatch = item.variation_percentage === 0;
                        }
                    }

                    // Min percentage filter
                    var percentageMatch = true;
                    if (!isNaN(filterMinPercentage) && filterMinPercentage > 0) {
                        percentageMatch = Math.abs(item.variation_percentage) >= filterMinPercentage;
                    }

                    return gisidMatch && typeMatch && percentageMatch;
                });

                renderTable(filteredData);
                updateResultCount(filteredData.length);
            }

            // Debounce function
            function debounce(func, delay) {
                let timeout;
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
            }

            const debouncedApplyFilters = debounce(applyFilters, 300);

            // Event listeners
            $("#filterGisid").on("keyup", debouncedApplyFilters);
            $("#filterVariationType").on("change", applyFilters);
            $("#filterMinPercentage").on("keyup", debouncedApplyFilters);

            // Copy GIS ID functionality
            $(document).on('click', '.copy-gisid', function(e) {
                e.preventDefault();
                const gisid = $(this).data('gisid');

                // Create temporary input
                const tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(gisid).select();
                document.execCommand('copy');
                tempInput.remove();

                // Show tooltip or alert
                const $btn = $(this);
                const originalIcon = $btn.html();
                $btn.html('<i class="fas fa-check text-success"></i>');
                setTimeout(() => {
                    $btn.html(originalIcon);
                }, 1000);
            });

            // Export functionality
            $("#exportBtn").on("click", function() {
                // Get current filtered data
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

                // Create CSV
                const headers = [
                    'GIS ID', 'Polygon Area (sq.ft)', 'Number of Floors', 'Floor Percentage',
                    'Basement', 'Calculated Area (sq.ft)', 'MIS Plot Area (sq.ft)',
                    'Area Variation (sq.ft)', 'Variation Percentage (%)', 'Assessment Count'
                ];

                const csvRows = [headers.join(',')];

                exportData.forEach(item => {
                    const row = [
                        `"${item.gisid}"`,
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

                // Download CSV
                const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `variation_data_ward_${@json($warddetail->ward_no ?? '')}_${new Date().toISOString().slice(0,19)}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            });
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

        /* Form Controls */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #1679AB;
            box-shadow: 0 0 0 0.2rem rgba(22, 121, 171, 0.25);
        }

        /* Table Styles */
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
            animation: rowFadeIn 0.3s ease forwards;
            opacity: 0;
        }

        @keyframes rowFadeIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .variation-table tbody tr:hover {
            background-color: rgba(22, 121, 171, 0.05);
            transform: scale(1.01);
        }

        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 20px;
        }

        /* Button Styles */
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

        /* Copy button */
        .copy-gisid {
            opacity: 0.6;
            transition: opacity 0.2s ease;
        }

        .copy-gisid:hover {
            opacity: 1;
        }

        /* Table responsive */
        @media(max-width: 1200px) {
            .variation-table {
                font-size: 12px;
            }
            .variation-table thead th,
            .variation-table tbody td {
                padding: 8px 6px;
            }
        }

        @media(max-width: 768px) {
            .dashboard-content-area {
                padding: 15px;
            }

            .stat-card {
                margin-bottom: 15px;
            }

            .variation-table thead th {
                font-size: 10px;
                padding: 8px 4px;
            }

            .variation-table tbody td {
                font-size: 10px;
                padding: 8px 4px;
            }

            .badge {
                font-size: 9px;
                padding: 4px 8px;
            }
        }

        /* Loading Animation */
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

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #1679AB;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #102C57;
        }

        /* Statistics Card Icons */
        .stat-icon i {
            font-size: 28px;
        }

        /* Text Colors */
        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-primary {
            color: #1679AB !important;
        }

        /* Table row colors */
        .table-warning {
            background-color: #fff3cd !important;
        }

        .table-danger {
            background-color: #f8d7da !important;
        }

        .table-success {
            background-color: #d4edda !important;
        }

        /* Label styling */
        label {
            font-size: 0.9rem;
        }
    </style>
@endpush
