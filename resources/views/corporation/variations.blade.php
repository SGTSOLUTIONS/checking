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
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_buildings) }}</h2>
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
                                {{ number_format($total_mis_area) }} <small>sq.ft</small>
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
                                {{ number_format($total_calculated_area) }} <small>sq.ft</small>
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
                                $variationClass = $total_variation >= 0 ? 'text-success' : 'text-danger';
                                $variationIcon = $total_variation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            @endphp
                            <h2 class="fw-bold mb-0 {{ $variationClass }}">
                                {{ number_format($total_variation) }} <small>sq.ft</small>
                            </h2>
                            <small class="{{ $variationClass }}">
                                <i class="fas {{ $variationIcon }}"></i>
                                {{ number_format($total_variation_percentage, 2) }}% variation
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
                                    Total Buildings: {{ number_format($total_buildings) }}
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
                                    <i class="fas fa-download me-1"></i> Export Current View
                                </button>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="text-dark fw-bold mb-2">Filter by GIS ID:</label>
                                <input type="text" class="form-control" id="filterGisid" placeholder="Enter GIS ID...">
                            </div>
                            <div class="col-md-3">
                                <label class="text-dark fw-bold mb-2">Filter by Variation Type:</label>
                                <select class="form-select" id="filterVariationType">
                                    <option value="all">All Variations</option>
                                    <option value="positive">Positive Variation (Extra Area)</option>
                                    <option value="negative">Negative Variation (Less Area)</option>
                                    <option value="zero">No Variation</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="text-dark fw-bold mb-2">Min Variation %:</label>
                                <input type="number" class="form-control" id="filterMinPercentage" placeholder="e.g., 10">
                            </div>
                            <div class="col-md-3">
                                <label class="text-dark fw-bold mb-2">&nbsp;</label>
                                <button class="btn btn-primary w-100" id="resetFilters">
                                    <i class="fas fa-undo-alt me-1"></i> Reset Filters
                                </button>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loadingIndicator" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading data...</p>
                        </div>

                        <!-- Table Container -->
                        <div class="table-responsive">
                            <table class="table table-hover variation-table" id="variationTable">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">GIS ID</th>
                                        <th style="width: 10%">Polygon Area</th>
                                        <th style="width: 8%">Floors</th>
                                        <th style="width: 8%">Floor %</th>
                                        <th style="width: 8%">Basement</th>
                                        <th style="width: 12%">Calculated Area</th>
                                        <th style="width: 12%">MIS Plot Area</th>
                                        <th style="width: 12%">Area Variation</th>
                                        <th style="width: 8%">Variation %</th>
                                        <th style="width: 7%">Assessments</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Section -->
                        <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                            <div class="mb-2 mb-sm-0">
                                <small class="text-muted" id="paginationInfo">
                                    Showing 0 to 0 of 0 entries
                                </small>
                            </div>
                            <div>
                                <nav>
                                    <ul class="pagination mb-0" id="pagination">
                                        <!-- Pagination will be inserted here -->
                                    </ul>
                                </nav>
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
            // State variables
            let currentPage = 1;
            let perPage = 20;
            let totalRecords = {{ $total_buildings }};
            let filters = {
                gisid: '',
                variation_type: 'all',
                min_percentage: ''
            };

            // Load initial data
            loadVariations();

            // Event listeners for filters
            let debounceTimer;
            $("#filterGisid").on("keyup", function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filters.gisid = $(this).val().toLowerCase().trim();
                    currentPage = 1;
                    loadVariations();
                }, 500);
            });

            $("#filterVariationType").on("change", function() {
                filters.variation_type = $(this).val();
                currentPage = 1;
                loadVariations();
            });

            $("#filterMinPercentage").on("keyup", function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filters.min_percentage = $(this).val();
                    currentPage = 1;
                    loadVariations();
                }, 500);
            });

            $("#resetFilters").on("click", function() {
                $("#filterGisid").val('');
                $("#filterVariationType").val('all');
                $("#filterMinPercentage").val('');
                filters = {
                    gisid: '',
                    variation_type: 'all',
                    min_percentage: ''
                };
                currentPage = 1;
                loadVariations();
            });

            $("#exportBtn").on("click", function() {
                exportData();
            });

            function loadVariations() {
                $("#loadingIndicator").removeClass('d-none');
                $("#tableBody").html('');
                $("#noDataMessage").addClass('d-none');

                $.ajax({
                    url: "{{ route('corporation.variations.data', $warddetail->ward_no) }}",
                    type: "GET",
                    data: {
                        page: currentPage,
                        per_page: perPage,
                        gisid: filters.gisid,
                        variation_type: filters.variation_type,
                        min_percentage: filters.min_percentage
                    },
                    success: function(response) {
                        $("#loadingIndicator").addClass('d-none');

                        if (response.success) {
                            renderTable(response.data);
                            renderPagination(response.pagination);
                            updatePaginationInfo(response.pagination);
                            totalRecords = response.pagination.total;
                        } else {
                            showError("Failed to load data");
                        }
                    },
                    error: function(xhr) {
                        $("#loadingIndicator").addClass('d-none');
                        console.error("Error loading variations:", xhr);
                        showError("Error loading data. Please try again.");
                    }
                });
            }

            function renderTable(data) {
                const $tbody = $("#tableBody");
                $tbody.empty();

                if (!data || data.length === 0) {
                    $("#noDataMessage").removeClass('d-none');
                    $("#variationTable").addClass('d-none');
                    $("#paginationContainer").addClass('d-none');
                    return;
                }

                $("#noDataMessage").addClass('d-none');
                $("#variationTable").removeClass('d-none');
                $("#paginationContainer").removeClass('d-none');

                data.forEach(function(item, index) {
                    // Determine row class
                    let rowClass = '';
                    if (item.variation_percentage > 0) {
                        rowClass = 'table-warning';
                    } else if (item.variation_percentage < 0) {
                        rowClass = 'table-danger';
                    } else {
                        rowClass = 'table-success';
                    }

                    let row = `
                        <tr class="${rowClass}" data-gisid="${item.gisid.toLowerCase()}">
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
                                ${item.variation_percentage > 0 ? '+' : ''}${formatNumber(item.variation_percentage)}%
                            </td>
                            <td>
                                <span class="badge bg-secondary">${item.assessment_count}</span>
                            </td>
                        </tr>
                    `;
                    $tbody.append(row);
                });

                // Animation for rows
                $('#tableBody tr').each(function(i) {
                    $(this).css('animation-delay', (i * 0.02) + 's');
                });
            }

            function renderPagination(pagination) {
                const $pagination = $("#pagination");
                $pagination.empty();

                if (pagination.total_pages <= 1) {
                    return;
                }

                // Previous button
                if (pagination.current_page > 1) {
                    $pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        </li>
                    `);
                } else {
                    $pagination.append(`
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-left"></i> Prev</span>
                        </li>
                    `);
                }

                // Page numbers
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

                if (startPage > 1) {
                    $pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>
                    `);
                    if (startPage > 2) {
                        $pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    if (i === pagination.current_page) {
                        $pagination.append(`
                            <li class="page-item active">
                                <span class="page-link">${i}</span>
                            </li>
                        `);
                    } else {
                        $pagination.append(`
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `);
                    }
                }

                if (endPage < pagination.total_pages) {
                    if (endPage < pagination.total_pages - 1) {
                        $pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                    $pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${pagination.total_pages}">${pagination.total_pages}</a>
                        </li>
                    `);
                }

                // Next button
                if (pagination.current_page < pagination.total_pages) {
                    $pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    `);
                } else {
                    $pagination.append(`
                        <li class="page-item disabled">
                            <span class="page-link">Next <i class="fas fa-chevron-right"></i></span>
                        </li>
                    `);
                }

                // Pagination click handler
                $pagination.find('a.page-link').on('click', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    if (page && page !== currentPage) {
                        currentPage = page;
                        loadVariations();
                        // Scroll to top of table
                        $('html, body').animate({
                            scrollTop: $("#variationTable").offset().top - 100
                        }, 300);
                    }
                });
            }

            function updatePaginationInfo(pagination) {
                const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
                const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
                $("#paginationInfo").text(`Showing ${start} to ${end} of ${pagination.total} entries`);
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

            function showError(message) {
                $("#tableBody").html(`
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="alert alert-danger m-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${message}
                            </div>
                        </td>
                    </tr>
                `);
            }

            // Copy GIS ID functionality
            $(document).on('click', '.copy-gisid', function(e) {
                e.preventDefault();
                const gisid = $(this).data('gisid');

                navigator.clipboard.writeText(gisid).then(function() {
                    const $btn = $(e.currentTarget);
                    const originalIcon = $btn.html();
                    $btn.html('<i class="fas fa-check text-success"></i>');
                    setTimeout(() => {
                        $btn.html(originalIcon);
                    }, 1000);
                }).catch(function() {
                    // Fallback for older browsers
                    const tempInput = $('<input>');
                    $('body').append(tempInput);
                    tempInput.val(gisid).select();
                    document.execCommand('copy');
                    tempInput.remove();

                    const $btn = $(e.currentTarget);
                    const originalIcon = $btn.html();
                    $btn.html('<i class="fas fa-check text-success"></i>');
                    setTimeout(() => {
                        $btn.html(originalIcon);
                    }, 1000);
                });
            });

            function exportData() {
                window.location.href = "{{ route('corporation.variations.export', $warddetail->ward_no) }}" +
                    "?gisid=" + encodeURIComponent(filters.gisid) +
                    "&variation_type=" + encodeURIComponent(filters.variation_type) +
                    "&min_percentage=" + encodeURIComponent(filters.min_percentage);
            }
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

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: #102C57;
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
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

        .stat-icon i {
            font-size: 28px;
        }

        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-primary { color: #1679AB !important; }

        .table-warning { background-color: #fff3cd !important; }
        .table-danger { background-color: #f8d7da !important; }
        .table-success { background-color: #d4edda !important; }
    </style>
@endpush
