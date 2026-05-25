{{-- resources/views/corporation/variations.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Variation Analysis')

@section('content')

<div class="dashboard-content-area">

    <div class="animate__animated animate__fadeInUp">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">

            <div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-light mb-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back
                </a>

                <h3 class="fw-bold text-white mt-2">
                    <i class="fas fa-chart-line me-2"></i>

                    Variation Analysis -
                    {{ $warddetail->zone ?? '' }}
                    Zone,
                    Ward {{ $warddetail->ward_no ?? '' }}
                </h3>
            </div>

            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="stat-card p-3">

                    <h6>Total Buildings</h6>

                    <h2 class="fw-bold">
                        {{ number_format($polygons->total()) }}
                    </h2>

                    <small class="text-muted">
                        GIS Polygons
                    </small>

                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card p-3">

                    <h6>Total MIS Area</h6>

                    <h2 class="fw-bold text-primary">
                        {{ number_format($totalMisAreaAll, 2) }}
                    </h2>

                    <small class="text-muted">
                        sq.ft
                    </small>

                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card p-3">

                    <h6>Total Calculated Area</h6>

                    <h2 class="fw-bold text-success">
                        {{ number_format($totalCalculatedAreaAll, 2) }}
                    </h2>

                    <small class="text-muted">
                        sq.ft
                    </small>

                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card p-3">

                    <h6>Total Variation</h6>

                    <h2 class="fw-bold {{ $totalVariationAll >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($totalVariationAll, 2) }}
                    </h2>

                    <small class="{{ $totalVariationAll >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format(abs($totalVariationPercentageAll), 2) }}%
                    </small>

                </div>
            </div>

        </div>

        {{-- FILTERS --}}
        <div class="stat-card p-4 mb-4">

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="fw-bold mb-2">
                        GIS ID
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="filterGisid"
                        placeholder="Search GIS ID"
                    >
                </div>

                <div class="col-md-4">
                    <label class="fw-bold mb-2">
                        Variation Type
                    </label>

                    <select class="form-select" id="filterVariationType">

                        <option value="all">
                            All
                        </option>

                        <option value="positive">
                            Extra Area
                        </option>

                        <option value="negative">
                            Less Area
                        </option>

                        <option value="zero">
                            Matched
                        </option>

                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold mb-2">
                        Minimum Variation %
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="filterMinPercentage"
                        placeholder="10"
                    >
                </div>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="stat-card p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h4 class="fw-bold">
                    Building Variation Details
                </h4>

                <button class="btn btn-outline-primary" id="exportBtn">
                    <i class="fas fa-download me-1"></i>
                    Export
                </button>

            </div>

            <div class="mb-3">

                <span class="badge bg-primary p-2">
                    Showing
                    <span id="resultCount">
                        {{ count($polygons) }}
                    </span>
                    Results
                </span>

            </div>

            <div class="table-responsive">

                <table class="table table-hover variation-table">

                    <thead>

                        <tr>

                            <th>GIS ID</th>

                            <th>Polygon Area</th>

                            <th>Floors</th>

                            <th>Floor %</th>

                            <th>Basement</th>

                            <th>Calculated Area</th>

                            <th>MIS Area</th>

                            <th>Variation</th>

                            <th>Variation %</th>

                            <th>Status</th>

                            <th>Assessments</th>

                        </tr>

                    </thead>

                    <tbody id="tableBody">

                        @forelse($polygons as $item)

                            @php

                                $rowClass =
                                    $item->variation_percentage > 0
                                        ? 'table-success'
                                        : ($item->variation_percentage < 0
                                            ? 'table-danger'
                                            : 'table-light');

                                $statusBadge =
                                    $item->variation_percentage > 0
                                        ? '<span class="badge bg-success">Extra Area</span>'
                                        : ($item->variation_percentage < 0
                                            ? '<span class="badge bg-danger">Less Area</span>'
                                            : '<span class="badge bg-secondary">Matched</span>');

                            @endphp

                            <tr class="{{ $rowClass }}">

                                <td>

                                    <strong>
                                        {{ $item->gisid }}
                                    </strong>

                                    <button
                                        class="btn btn-sm btn-link copy-gisid"
                                        data-gisid="{{ $item->gisid }}"
                                    >
                                        <i class="fas fa-copy"></i>
                                    </button>

                                </td>

                                <td>
                                    {{ number_format($item->sqfeet, 2) }}
                                </td>

                                <td>
                                    {{ $item->number_floor }}
                                </td>

                                <td>
                                    {{ $item->percentage }}%
                                </td>

                                <td>
                                    {{ $item->basement > 0 ? $item->basement : '-' }}
                                </td>

                                <td class="fw-bold">
                                    {{ number_format($item->calculated_area, 2) }}
                                </td>

                                <td>
                                    {{ number_format($item->mis_plot_area, 2) }}
                                </td>

                                <td class="{{ $item->area_variation > 0 ? 'text-success' : ($item->area_variation < 0 ? 'text-danger' : '') }}">

                                    {{ $item->area_variation > 0 ? '+' : '' }}

                                    {{ number_format($item->area_variation, 2) }}

                                </td>

                                <td class="{{ $item->variation_percentage > 0 ? 'text-success' : ($item->variation_percentage < 0 ? 'text-danger' : 'text-secondary') }} fw-bold">

                                    {{ $item->variation_percentage > 0 ? '+' : '' }}

                                    {{ number_format($item->variation_percentage, 2) }}%

                                </td>

                                <td>
                                    {!! $statusBadge !!}
                                </td>

                                <td>
                                    <span class="badge bg-dark">
                                        {{ $item->assessment_count }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="11" class="text-center py-5">

                                    <div class="alert alert-info">

                                        No variation data found

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">

                {{ $polygons->links() }}

            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>

$(document).ready(function() {

    const variationsData = @json($polygons->items());

    function formatNumber(num) {

        return parseFloat(num || 0).toLocaleString('en-IN', {

            minimumFractionDigits: 2,
            maximumFractionDigits: 2

        });

    }

    function applyFilters() {

        const filterGisid =
            $("#filterGisid").val().toLowerCase().trim();

        const filterVariationType =
            $("#filterVariationType").val();

        const filterMinPercentage =
            parseFloat($("#filterMinPercentage").val());

        const filteredData = variationsData.filter(function(item) {

            const gisid =
                (item.gisid || '').toString().toLowerCase();

            const gisidMatch =
                filterGisid === '' ||
                gisid.includes(filterGisid);

            const typeMatch =
                filterVariationType === 'all' ||

                (filterVariationType === 'positive' &&
                    item.variation_percentage > 0) ||

                (filterVariationType === 'negative' &&
                    item.variation_percentage < 0) ||

                (filterVariationType === 'zero' &&
                    item.variation_percentage == 0);

            const percentageMatch =
                isNaN(filterMinPercentage) ||
                Math.abs(item.variation_percentage) >= filterMinPercentage;

            return gisidMatch &&
                   typeMatch &&
                   percentageMatch;

        });

        renderTable(filteredData);

    }

    function renderTable(data) {

        let html = '';

        if (data.length === 0) {

            html = `
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <div class="alert alert-info">
                            No matching records found
                        </div>
                    </td>
                </tr>
            `;

            $("#tableBody").html(html);

            $("#resultCount").text(0);

            return;

        }

        data.forEach(function(item) {

            let rowClass = '';

            let badge = '';

            if (item.variation_percentage > 0) {

                rowClass = 'table-success';

                badge =
                    '<span class="badge bg-success">Extra Area</span>';

            }
            else if (item.variation_percentage < 0) {

                rowClass = 'table-danger';

                badge =
                    '<span class="badge bg-danger">Less Area</span>';

            }
            else {

                rowClass = 'table-light';

                badge =
                    '<span class="badge bg-secondary">Matched</span>';

            }

            html += `
                <tr class="${rowClass}">

                    <td>
                        <strong>${item.gisid}</strong>
                    </td>

                    <td>${formatNumber(item.sqfeet)}</td>

                    <td>${item.number_floor}</td>

                    <td>${item.percentage}%</td>

                    <td>${item.basement > 0 ? item.basement : '-'}</td>

                    <td class="fw-bold">
                        ${formatNumber(item.calculated_area)}
                    </td>

                    <td>
                        ${formatNumber(item.mis_plot_area)}
                    </td>

                    <td class="${item.area_variation > 0 ? 'text-success' : (item.area_variation < 0 ? 'text-danger' : '')}">
                        ${item.area_variation > 0 ? '+' : ''}
                        ${formatNumber(item.area_variation)}
                    </td>

                    <td class="${item.variation_percentage > 0 ? 'text-success' : (item.variation_percentage < 0 ? 'text-danger' : 'text-secondary')} fw-bold">

                        ${item.variation_percentage > 0 ? '+' : ''}

                        ${parseFloat(item.variation_percentage).toFixed(2)}%

                    </td>

                    <td>${badge}</td>

                    <td>
                        <span class="badge bg-dark">
                            ${item.assessment_count}
                        </span>
                    </td>

                </tr>
            `;

        });

        $("#tableBody").html(html);

        $("#resultCount").text(data.length);

    }

    $("#filterGisid").on('keyup', applyFilters);

    $("#filterVariationType").on('change', applyFilters);

    $("#filterMinPercentage").on('keyup', applyFilters);

    $("#exportBtn").on("click", function() {

        let csv = [];

        csv.push([
            'GIS ID',
            'Polygon Area',
            'Floors',
            'Floor %',
            'Basement',
            'Calculated Area',
            'MIS Area',
            'Variation',
            'Variation %',
            'Assessments'
        ].join(','));

        variationsData.forEach(function(item) {

            csv.push([

                item.gisid,
                item.sqfeet,
                item.number_floor,
                item.percentage,
                item.basement,
                item.calculated_area,
                item.mis_plot_area,
                item.area_variation,
                item.variation_percentage,
                item.assessment_count

            ].join(','));

        });

        let blob =
            new Blob([csv.join("\n")], {
                type: 'text/csv'
            });

        let link =
            document.createElement("a");

        link.href =
            URL.createObjectURL(blob);

        link.download =
            "variation_report.csv";

        link.click();

    });

});

</script>

<style>

.dashboard-content-area {

    padding: 20px;

    background:
        linear-gradient(
            135deg,
            #102C57 0%,
            #1679AB 100%
        );

    min-height: 100vh;

}

.stat-card {

    background: white;

    border-radius: 20px;

    box-shadow:
        0 10px 30px rgba(0,0,0,0.1);

}

.variation-table thead th {

    background:
        linear-gradient(
            135deg,
            #102C57 0%,
            #1679AB 100%
        );

    color: white;

    border: none;

    white-space: nowrap;

}

.variation-table tbody td {

    vertical-align: middle;

}

.table-success {

    background-color: #d1f7dc !important;

}

.table-danger {

    background-color: #ffd7d7 !important;

}

.table-light {

    background-color: #f5f5f5 !important;

}

.form-control,
.form-select {

    border-radius: 10px;

}

.btn-outline-primary {

    border-radius: 10px;

}

.badge {

    border-radius: 20px;

}

</style>

@endpush
