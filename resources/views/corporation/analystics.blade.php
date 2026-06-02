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

        <!-- Statistics Cards -->
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
                    <div class="stat-icon"><i class="fas fa-ruler-combined"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">MIS Plot Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalMisPlotArea, 2) }}</h2>
                        <small class="text-warning"><i class="fas fa-database"></i> From MIS records</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Calculated Area</h6>
                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalCalculatedArea, 2) }}</h2>
                        <small class="text-danger"><i class="fas fa-calculator"></i> Based on formula</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calculator"></i></div>
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
                <div class="col-md-12">
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
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="stat-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-filter me-2" style="color:#1679AB;"></i>
                    Advanced Filters
                </h5>
                <button class="btn btn-sm btn-outline-secondary" id="clearFiltersBtn">
                    <i class="fas fa-undo-alt me-1"></i> Clear All Filters
                </button>
            </div>

            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">GIS ID</label>
                    <input type="text" id="gisidSearch" class="form-control" placeholder="Enter GIS ID...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Floors</label>
                    <select id="floorsFilter" class="form-select">
                        <option value="">All</option>
                        <option value="1">1 Floor</option>
                        <option value="2">2 Floors</option>
                        <option value="3">3 Floors</option>
                        <option value="4">4+ Floors</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Variation Type</label>
                    <select id="variationTypeFilter" class="form-select">
                        <option value="">All</option>
                        <option value="positive">Under-assessment (Positive)</option>
                        <option value="negative">Over-assessment (Negative)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Min Variation %</label>
                    <input type="number" id="variationMin" class="form-control" placeholder="Min %" step="any">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Max Variation %</label>
                    <input type="number" id="variationMax" class="form-control" placeholder="Max %" step="any">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Tax/Balance Filter</label>
                    <select id="taxBalanceFilter" class="form-select">
                        <option value="">All</option>
                        <option value="has_tax">Has Tax Due</option>
                        <option value="no_tax">No Tax Due</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-table me-2" style="color:#1679AB;"></i>
                    Detailed Building Data
                    <span class="badge bg-primary ms-2" id="recordCount">0 Records</span>
                </h4>
                <div class="mt-2 mt-sm-0">
                    <label class="me-2">Show:</label>
                    <select id="perPageSelect" class="form-select form-select-sm d-inline-block w-auto">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50" selected>50 per page</option>
                        <option value="100">100 per page</option>
                        <option value="-1">All Records</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="variationsTable">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="index" style="cursor: pointer;">S.No <i class="fas fa-sort"></i></th>
                            <th class="sortable" data-column="gisid" style="cursor: pointer;">GIS ID <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="sqfeet" style="cursor: pointer;">Sq. Feet <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="number_floor" style="cursor: pointer;">Floors <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="percentage" style="cursor: pointer;">Floor % <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="basement" style="cursor: pointer;">Basement <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="mis_plot_area" style="cursor: pointer;">MIS Area <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="calculated_area" style="cursor: pointer;">Calculated <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="area_variation" style="cursor: pointer;">Variation <i class="fas fa-sort"></i></th>
                            <th class="sortable text-center" data-column="variation_percentage" style="cursor: pointer;">Var % <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="half_year_tax" style="cursor: pointer;">Half Year Tax <i class="fas fa-sort"></i></th>
                            <th class="sortable text-end" data-column="tax_balance" style="cursor: pointer;">Balance <i class="fas fa-sort"></i></th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Map View</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="14" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading data...</p>
                        </td></tr>
                    </tbody>
                    <tfoot id="tableFooter" style="background: #f8f9fa;"></tfoot>
                </table>
            </div>

            <div id="pagination" class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2"></div>
        </div>

        <!-- Info Note -->
        <div class="stat-card p-3 mt-4" style="background: #e7f3ff;">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3" style="color:#1679AB;"></i>
                <div>
                    <strong>Note:</strong>
                    Click on <i class="fas fa-map-marker-alt"></i> <strong>GIS ID</strong> or <strong>View Map</strong> button to see the location on Google Maps.
                    <span class="ms-3"><span class="badge bg-success">Positive Variation</span> = Under-assessment (Building larger than MIS record)</span>
                    <span class="ms-3"><span class="badge bg-danger">Negative Variation</span> = Over-assessment (Building smaller than MIS record)</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Proj4js for EPSG:3857 to EPSG:4326 conversion -->
<script src="https://cdn.jsdelivr.net/npm/proj4@2.9.0/dist/proj4.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    // Define the projection systems
    const webMercator = '+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +no_defs +type=crs';
    const wgs84 = '+proj=longlat +datum=WGS84 +no_defs +type=crs';

    proj4.defs('EPSG:3857', webMercator);
    proj4.defs('EPSG:4326', wgs84);

    function convert3857ToLatLng(x, y) {
        try {
            const result = proj4('EPSG:3857', 'EPSG:4326', [x, y]);
            return { lng: result[0], lat: result[1] };
        } catch (error) {
            console.error('Conversion error:', error);
            return null;
        }
    }

    function parseCoordinates(coordString) {
        if (!coordString) return null;
        try {
            let cleaned = coordString.replace(/[\[\]]/g, '');
            let parts = cleaned.split(',');
            if (parts.length >= 2) {
                let x = parseFloat(parts[0]);
                let y = parseFloat(parts[1]);
                if (!isNaN(x) && !isNaN(y)) {
                    return { x, y };
                }
            }
        } catch (e) {
            console.error('Parse error:', e);
        }
        return null;
    }

    let allData = @json($allDataJson);

    if (typeof allData === 'string') {
        allData = JSON.parse(allData);
    }

    allData = allData.map(record => {
        const coords = parseCoordinates(record.coordinates);
        if (coords) {
            const latLng = convert3857ToLatLng(coords.x, coords.y);
            if (latLng && !isNaN(latLng.lat) && !isNaN(latLng.lng)) {
                record.lat = latLng.lat;
                record.lng = latLng.lng;
                record.hasValidCoords = true;
            } else {
                record.hasValidCoords = false;
            }
        } else {
            record.hasValidCoords = false;
        }
        return record;
    });

    let filteredData = [...allData];
    let currentPage = 1;
    let itemsPerPage = 50;
    let currentSort = { column: 'index', direction: 'asc' };

    const serverTotals = {
        totalBuildings: {{ $totalBuildings }},
        totalSqfeet: {{ $totalSqfeet }},
        totalMisPlotArea: {{ $totalMisPlotArea }},
        totalCalculatedArea: {{ $totalCalculatedArea }},
        totalAreaVariation: {{ $totalAreaVariation }},
        avgVariationPercentage: {{ $avgVariationPercentage }}
    };

    $(document).ready(function() {
        console.log('Total records loaded:', allData.length);
        initializeFilters();
        applyFiltersAndRender();
    });

    function initializeFilters() {
        $('#applyFiltersBtn').click(function() {
            currentPage = 1;
            applyFiltersAndRender();
        });

        $('#gisidSearch, #floorsFilter, #variationTypeFilter, #variationMin, #variationMax, #taxBalanceFilter').on('keypress', function(e) {
            if (e.which === 13) {
                currentPage = 1;
                applyFiltersAndRender();
            }
        });

        $('#clearFiltersBtn').click(function() {
            $('#gisidSearch').val('');
            $('#floorsFilter').val('');
            $('#variationTypeFilter').val('');
            $('#variationMin').val('');
            $('#variationMax').val('');
            $('#taxBalanceFilter').val('');
            currentPage = 1;
            applyFiltersAndRender();
        });

        $('#perPageSelect').change(function() {
            const val = $(this).val();
            itemsPerPage = val === '-1' ? filteredData.length : parseInt(val);
            currentPage = 1;
            renderTable();
        });

        $('.sortable').click(function() {
            const column = $(this).data('column');
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = column;
                currentSort.direction = 'asc';
            }
            applyFiltersAndRender();
        });
    }

    function applyFiltersAndRender() {
        filteredData = [...allData];

        const gisidSearch = $('#gisidSearch').val().toLowerCase().trim();
        if (gisidSearch) {
            filteredData = filteredData.filter(item => item.gisid.toLowerCase().includes(gisidSearch));
        }

        const floorsFilter = $('#floorsFilter').val();
        if (floorsFilter) {
            filteredData = filteredData.filter(item => {
                if (floorsFilter === '4') return item.number_floor >= 4;
                return item.number_floor == floorsFilter;
            });
        }

        const variationType = $('#variationTypeFilter').val();
        if (variationType) {
            filteredData = filteredData.filter(item => {
                if (variationType === 'positive') return item.area_variation > 0;
                if (variationType === 'negative') return item.area_variation < 0;
                return true;
            });
        }

        const variationMin = parseFloat($('#variationMin').val());
        if (!isNaN(variationMin)) {
            filteredData = filteredData.filter(item => item.variation_percentage >= variationMin);
        }

        const variationMax = parseFloat($('#variationMax').val());
        if (!isNaN(variationMax)) {
            filteredData = filteredData.filter(item => item.variation_percentage <= variationMax);
        }

        const taxBalanceFilter = $('#taxBalanceFilter').val();
        if (taxBalanceFilter === 'has_tax') {
            filteredData = filteredData.filter(item => item.tax_balance > 0);
        } else if (taxBalanceFilter === 'no_tax') {
            filteredData = filteredData.filter(item => item.tax_balance <= 0);
        }

        filteredData.sort((a, b) => {
            let aVal = a[currentSort.column];
            let bVal = b[currentSort.column];

            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        $('#recordCount').text(filteredData.length + ' Records');
        currentPage = 1;
        renderTable();
    }

    function openGoogleMaps(gisid, lat, lng) {
        let url;
        if (lat && lng && !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            url = `https://www.google.com/maps?q=${lat},${lng}&z=18`;
        } else {
            url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent('Building ' + gisid)}`;
        }
        window.open(url, '_blank');
    }

    function renderTable() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const pageData = filteredData.slice(start, start + itemsPerPage);

        let filteredSqfeet = 0, filteredMisArea = 0, filteredCalculated = 0, filteredVariation = 0;
        let filteredTax = 0, filteredBalance = 0;

        filteredData.forEach(item => {
            filteredSqfeet += item.sqfeet;
            filteredMisArea += item.mis_plot_area;
            filteredCalculated += item.calculated_area;
            filteredVariation += item.area_variation;
            filteredTax += item.half_year_tax || 0;
            filteredBalance += item.tax_balance || 0;
        });
        const filteredAvgVariation = filteredMisArea > 0 ? (filteredVariation / filteredMisArea) * 100 : 0;

        let html = '';
        if (pageData.length === 0) {
            html = '<tr><td colspan="14" class="text-center py-5">No records found</td></tr>';
        } else {
            pageData.forEach((item, idx) => {
                const variationClass = item.area_variation >= 0 ? 'text-success' : 'text-danger';
                const variationIcon = item.area_variation >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                const hasValidCoords = item.hasValidCoords === true;

                html += `
                    <tr>
                        <td>${start + idx + 1}</td>
                        <td>
                            <a href="javascript:void(0)" onclick="openGoogleMaps('${item.gisid}', ${item.lat || 'null'}, ${item.lng || 'null'})"
                               style="color: #1679AB; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <strong>${item.gisid}</strong>
                            </a>
                        </td>
                        <td class="text-end">${item.sqfeet.toFixed(2)}</td>
                        <td class="text-center"><span class="badge bg-primary">${item.number_floor}</span></td>
                        <td class="text-center">${item.percentage}%</td>
                        <td class="text-center">${item.basement > 0 ? item.basement : '-'}</td>
                        <td class="text-end">${item.mis_plot_area.toFixed(2)}</td>
                        <td class="text-end">${item.calculated_area.toFixed(2)}</td>
                        <td class="text-end ${variationClass}">
                            <i class="fas ${variationIcon} me-1"></i>
                            ${item.area_variation >= 0 ? '+' : ''}${item.area_variation.toFixed(2)}
                        </td>
                        <td class="text-center ${variationClass}">
                            <strong>${item.variation_percentage >= 0 ? '+' : ''}${item.variation_percentage.toFixed(2)}%</strong>
                        </td>
                        <td class="text-end">${(item.half_year_tax || 0).toFixed(2)}</td>
                        <td class="text-end ${item.tax_balance > 0 ? 'text-danger' : 'text-success'}">
                            ${(item.tax_balance || 0).toFixed(2)}
                        </td>
                        <td class="text-center">
                            <span class="badge ${item.assessment_count > 1 ? 'bg-info' : 'bg-secondary'}">${item.assessment_count}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm ${hasValidCoords ? 'btn-primary' : 'btn-secondary'}"
                                    onclick="openGoogleMaps('${item.gisid}', ${item.lat || 'null'}, ${item.lng || 'null'})"
                                    ${!hasValidCoords ? 'disabled' : ''}>
                                <i class="fas fa-map-marked-alt"></i> View Map
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        $('#tableBody').html(html);

        const footerHtml = `
            <tr style="border-top: 2px solid #dee2e6; background-color: #f8f9fa;">
                <td colspan="2"><strong>FILTERED TOTAL</strong></td>
                <td class="text-end"><strong>${filteredSqfeet.toFixed(2)}</strong></td>
                <td colspan="3"></td>
                <td class="text-end"><strong>${filteredMisArea.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${filteredCalculated.toFixed(2)}</strong></td>
                <td class="text-end ${filteredVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredVariation >= 0 ? '+' : ''}${filteredVariation.toFixed(2)}</strong>
                </td>
                <td class="text-center ${filteredAvgVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${filteredAvgVariation >= 0 ? '+' : ''}${filteredAvgVariation.toFixed(2)}%</strong>
                </td>
                <td class="text-end"><strong>${filteredTax.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${filteredBalance.toFixed(2)}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr style="background: #e9ecef;">
                <td colspan="2"><strong>WARD TOTAL</strong></td>
                <td class="text-end"><strong>${serverTotals.totalSqfeet.toFixed(2)}</strong></td>
                <td colspan="3"></td>
                <td class="text-end"><strong>${serverTotals.totalMisPlotArea.toFixed(2)}</strong></td>
                <td class="text-end"><strong>${serverTotals.totalCalculatedArea.toFixed(2)}</strong></td>
                <td class="text-end ${serverTotals.totalAreaVariation >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.totalAreaVariation >= 0 ? '+' : ''}${serverTotals.totalAreaVariation.toFixed(2)}</strong>
                </td>
                <td class="text-center ${serverTotals.avgVariationPercentage >= 0 ? 'text-success' : 'text-danger'}">
                    <strong>${serverTotals.avgVariationPercentage >= 0 ? '+' : ''}${serverTotals.avgVariationPercentage.toFixed(2)}%</strong>
                </td>
                <td colspan="3"></td>
            </tr>
        `;
        $('#tableFooter').html(footerHtml);

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1 && filteredData.length <= itemsPerPage) {
            $('#pagination').html(`
                <div class="text-muted">
                    Showing ${filteredData.length} of ${filteredData.length} records
                </div>
            `);
            return;
        }

        const startRecord = (currentPage - 1) * itemsPerPage + 1;
        const endRecord = Math.min(currentPage * itemsPerPage, filteredData.length);

        let paginationHtml = `
            <div class="text-muted">
                Showing ${startRecord} to ${endRecord} of ${filteredData.length} records
            </div>
            <nav>
                <ul class="pagination mb-0">
        `;

        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">« Previous</a>
            </li>
        `;

        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1); return false;">1</a></li>`;
            if (startPage > 2) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a></li>`;
        }

        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next »</a>
            </li>
        `;

        paginationHtml += `</ul></nav>`;
        $('#pagination').html(paginationHtml);
    }

    function changePage(page) {
        if (page < 1 || page > Math.ceil(filteredData.length / itemsPerPage)) return;
        currentPage = page;
        renderTable();
    }

    function exportToExcel() {
        const exportData = filteredData.map(item => ({
            'S.No': item.index,
            'GIS ID': item.gisid,
            'Sq. Feet': item.sqfeet,
            'Floors': item.number_floor,
            'Floor %': item.percentage,
            'Basement': item.basement,
            'MIS Plot Area': item.mis_plot_area,
            'Calculated Area': item.calculated_area,
            'Area Variation': item.area_variation,
            'Variation %': item.variation_percentage,
            'Half Year Tax': item.half_year_tax || 0,
            'Tax Balance': item.tax_balance || 0,
            'Assessment Count': item.assessment_count,
            'Latitude': item.lat || '',
            'Longitude': item.lng || ''
        }));

        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Building_Variations');

        ws['!cols'] = Object.keys(exportData[0] || {}).map(() => ({ wch: 20 }));

        XLSX.writeFile(wb, `Ward_${new Date().getTime()}_Building_Variations.xlsx`);
    }
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
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead th {
        padding: 15px;
        font-weight: 600;
        font-size: 0.85rem;
        background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        color: #ffffff !important;
        border: none;
        white-space: nowrap;
    }

    .table thead th i {
        margin-left: 5px;
        opacity: 0.7;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background-color: rgba(22, 121, 171, 0.05);
    }

    .table tfoot td {
        padding: 12px 15px;
        font-weight: bold;
    }

    .btn-sm {
        border-radius: 8px;
        padding: 5px 12px;
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

    .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 20px;
    }

    .form-label {
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }

    @media (max-width: 768px) {
        .dashboard-content-area {
            padding: 15px;
        }
        .table {
            font-size: 0.75rem;
        }
        .table thead th,
        .table tbody td {
            padding: 8px 10px;
        }
        .btn-sm {
            padding: 3px 8px;
            font-size: 0.7rem;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
    }

    @media print {
        .btn, .pagination, #perPageSelect, .stat-card .btn, .filters-section, #clearFiltersBtn, #applyFiltersBtn {
            display: none !important;
        }
        .stat-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .table {
            font-size: 10pt;
        }
        .dashboard-content-area {
            background: white;
            padding: 10px;
        }
    }
</style>
@endpush
