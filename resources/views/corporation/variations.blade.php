{{-- resources/views/corporation/variations.blade.php --}}

@extends('layouts.commissioner')

@section('title', 'Variation Analysis')

@section('content')

    @php

        $totalBuildings = $result->total();

        $totalMisAreaAll = collect($result->items())->sum('mis_plot_area');

        $totalCalculatedAreaAll = collect($result->items())->sum('calculated_area');

        $totalVariationAll = collect($result->items())->sum('area_variation');

        $totalVariationPercentageAll = $totalMisAreaAll > 0 ? ($totalVariationAll / $totalMisAreaAll) * 100 : 0;

    @endphp

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

                            {{ number_format($totalBuildings) }}

                        </h2>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="stat-card p-3">

                        <h6>Total MIS Area</h6>

                        <h2 class="fw-bold text-primary">

                            {{ number_format($totalMisAreaAll, 2) }}

                        </h2>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="stat-card p-3">

                        <h6>Total Calculated Area</h6>

                        <h2 class="fw-bold text-success">

                            {{ number_format($totalCalculatedAreaAll, 2) }}

                        </h2>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="stat-card p-3">

                        <h6>Total Variation</h6>

                        <h2 class="fw-bold {{ $totalVariationAll >= 0 ? 'text-success' : 'text-danger' }}">

                            {{ number_format($totalVariationAll, 2) }}

                        </h2>

                    </div>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="stat-card p-4">

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

                                <th>Assessments</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($result as $item)
                                <tr>

                                    <td>

                                        <strong>

                                            {{ $item['gisid'] }}

                                        </strong>

                                    </td>

                                    <td>

                                        {{ number_format($item['sqfeet'], 2) }}

                                    </td>

                                    <td>

                                        {{ $item['number_floor'] }}

                                    </td>

                                    <td>

                                        {{ $item['percentage'] }}%

                                    </td>

                                    <td>

                                        {{ $item['basement'] }}

                                    </td>

                                    <td>

                                        {{ number_format($item['calculated_area'], 2) }}

                                    </td>

                                    <td>

                                        {{ number_format($item['mis_plot_area'], 2) }}

                                    </td>

                                    <td class="{{ $item['area_variation'] > 0 ? 'text-success' : 'text-danger' }}">

                                        {{ number_format($item['area_variation'], 2) }}

                                    </td>

                                    <td class="{{ $item['variation_percentage'] > 0 ? 'text-success' : 'text-danger' }}">

                                        {{ number_format($item['variation_percentage'], 2) }}%

                                    </td>

                                    <td>

                                        {{ $item['assessment_count'] }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="10" class="text-center">

                                        No Data Found

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                <div class="mt-4 d-flex justify-content-center">

                    {{-- PAGINATION --}}
                    @if ($result->hasPages())

                        <div class="custom-pagination-wrapper">

                            {{-- LEFT INFO --}}
                            <div class="pagination-info">

                                Showing

                                <span class="info-highlight">
                                    {{ $result->firstItem() }}
                                </span>

                                to

                                <span class="info-highlight">
                                    {{ $result->lastItem() }}
                                </span>

                                of

                                <span class="info-highlight">
                                    {{ number_format($result->total()) }}
                                </span>

                                records

                            </div>

                            {{-- PAGINATION --}}
                            <div class="custom-pagination">

                                {{-- PREVIOUS --}}
                                @if ($result->onFirstPage())
                                    <span class="page-btn disabled">

                                        <i class="fas fa-chevron-left"></i>

                                    </span>
                                @else
                                    <a href="{{ $result->previousPageUrl() }}" class="page-btn nav-btn">

                                        <i class="fas fa-chevron-left"></i>

                                    </a>
                                @endif

                                {{-- FIRST PAGE --}}
                                @if ($result->currentPage() > 3)

                                    <a href="{{ $result->url(1) }}" class="page-btn">

                                        1

                                    </a>

                                    @if ($result->currentPage() > 4)
                                        <span class="pagination-dots">
                                            ...
                                        </span>
                                    @endif

                                @endif

                                {{-- PAGE NUMBERS --}}
                                @foreach (range(max(1, $result->currentPage() - 2), min($result->lastPage(), $result->currentPage() + 2)) as $page)
                                    @if ($page == $result->currentPage())
                                        <span class="page-btn active">

                                            {{ $page }}

                                        </span>
                                    @else
                                        <a href="{{ $result->url($page) }}" class="page-btn">

                                            {{ $page }}

                                        </a>
                                    @endif
                                @endforeach

                                {{-- LAST PAGE --}}
                                @if ($result->currentPage() < $result->lastPage() - 2)

                                    @if ($result->currentPage() < $result->lastPage() - 3)
                                        <span class="pagination-dots">
                                            ...
                                        </span>
                                    @endif

                                    <a href="{{ $result->url($result->lastPage()) }}" class="page-btn">

                                        {{ $result->lastPage() }}

                                    </a>

                                @endif

                                {{-- NEXT --}}
                                @if ($result->hasMorePages())
                                    <a href="{{ $result->nextPageUrl() }}" class="page-btn nav-btn">

                                        <i class="fas fa-chevron-right"></i>

                                    </a>
                                @else
                                    <span class="page-btn disabled">

                                        <i class="fas fa-chevron-right"></i>

                                    </span>
                                @endif

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection

@push('styles')
    <style>
        .dashboard-content-area {

            padding: 20px;

            background:
                linear-gradient(135deg,
                    #102C57 0%,
                    #1679AB 100%);

            min-height: 100vh;

        }

        .stat-card {

            background: white;

            border-radius: 20px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.1);

        }

        .variation-table thead th {

            background:
                linear-gradient(135deg,
                    #102C57 0%,
                    #1679AB 100%);

            color: white;

        }
    </style>
@endpush
