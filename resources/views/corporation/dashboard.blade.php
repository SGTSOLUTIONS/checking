@extends('layouts.commissioner')

@section('title', 'Commissioner Dashboard')

@section('content')

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-1">
                            Commissioner Dashboard
                        </h3>

                        <p class="text-muted mb-0">
                            Corporation :
                            <strong>
                                {{ $corporation->corporation_name ?? 'N/A' }}
                            </strong>
                        </p>
                    </div>

                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="row mb-4">

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">

                    <h6>Total Wards</h6>

                    <h2 class="fw-bold">
                        {{ $ward_count }}
                    </h2>

                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">

                    <h6>Total MIS Records</h6>

                    <h2 class="fw-bold">
                        {{ number_format($mis_count) }}
                    </h2>

                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">

                    <h6>Total Collections</h6>

                    <h2 class="fw-bold">
                        {{ count($collections) }}
                    </h2>

                </div>
            </div>
        </div>

    </div>

    <!-- ZONE / WARD DATA -->
    <div class="row">

        @foreach($collections as $collection)

        <div class="col-xl-6 col-lg-6 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-dark text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Zone :
                                {{ $collection['zone'] }}
                            </h5>

                            <small>
                                Ward :
                                {{ $collection['ward_no'] }}
                            </small>
                        </div>

                        <span class="badge bg-primary">
                            Ward {{ $collection['ward_no'] }}
                        </span>

                    </div>

                </div>

                <div class="card-body">

                    <!-- TABLE NAMES -->

                    <div class="mb-3">

                        <h6 class="fw-bold text-primary">
                            Table Names
                        </h6>

                        <table class="table table-bordered table-sm">

                            <tr>
                                <th width="40%">Point Table</th>
                                <td>
                                    {{ $collection['pointdatatable'] ?? 'N/A' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Polygon Data Table</th>
                                <td>
                                    {{ $collection['polygondatatable'] ?? 'N/A' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Polygon Table</th>
                                <td>
                                    {{ $collection['polygontable'] ?? 'N/A' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Road Table</th>
                                <td>
                                    {{ $collection['roadtable'] ?? 'N/A' }}
                                </td>
                            </tr>

                        </table>

                    </div>

                    <!-- COUNTS -->

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <div class="card bg-light border-0">

                                <div class="card-body text-center">

                                    <h6 class="text-muted">
                                        Total Buildings
                                    </h6>

                                    <h2 class="fw-bold text-primary">
                                        {{ number_format($collection['buildingCount']) }}
                                    </h2>

                                    <small class="text-muted">
                                        Unique GIS Buildings
                                    </small>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <div class="card bg-light border-0">

                                <div class="card-body text-center">

                                    <h6 class="text-muted">
                                        Surveyed Buildings
                                    </h6>

                                    <h2 class="fw-bold text-success">
                                        {{ number_format($collection['surveyedBuildingCount']) }}
                                    </h2>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <div class="card bg-light border-0">

                                <div class="card-body text-center">

                                    <h6 class="text-muted">
                                        Point Count
                                    </h6>

                                    <h2 class="fw-bold text-info">
                                        {{ number_format($collection['pointCount']) }}
                                    </h2>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <div class="card bg-light border-0">

                                <div class="card-body text-center">

                                    <h6 class="text-muted">
                                        Road Count
                                    </h6>

                                    <h2 class="fw-bold text-danger">
                                        {{ number_format($collection['roadCount']) }}
                                    </h2>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-12 mb-3">

                            <div class="card bg-light border-0">

                                <div class="card-body text-center">

                                    <h6 class="text-muted">
                                        MIS Records
                                    </h6>

                                    <h2 class="fw-bold text-dark">
                                        {{ number_format($collection['misCount']) }}
                                    </h2>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- VIEW DETAILS -->

                    <div class="mt-3 text-center">

                        <button
                            class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="collapse"
                            data-bs-target="#details{{ $loop->index }}"
                        >
                            View Details
                        </button>

                    </div>

                    <!-- COLLAPSE -->

                    <div
                        class="collapse mt-3"
                        id="details{{ $loop->index }}"
                    >

                        <!-- POINT DATA -->

                        <div class="mb-4">

                            <h6 class="fw-bold text-primary">
                                Point Data
                            </h6>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped table-sm">

                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>GIS ID</th>
                                            <th>Owner</th>
                                            <th>Door No</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($collection['pointData'] as $point)

                                        <tr>

                                            <td>
                                                {{ $point->id ?? '' }}
                                            </td>

                                            <td>
                                                {{ $point->gisid ?? '' }}
                                            </td>

                                            <td>
                                                {{ $point->owner_name ?? '' }}
                                            </td>

                                            <td>
                                                {{ $point->new_door_no ?? '' }}
                                            </td>

                                        </tr>

                                        @empty

                                        <tr>
                                            <td colspan="4" class="text-center">
                                                No Point Data
                                            </td>
                                        </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <!-- MIS DATA -->

                        <div class="mb-4">

                            <h6 class="fw-bold text-danger">
                                MIS Data
                            </h6>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped table-sm">

                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Owner Name</th>
                                            <th>Old Door</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($collection['misData'] as $mis)

                                        <tr>

                                            <td>
                                                {{ $mis->id ?? '' }}
                                            </td>

                                            <td>
                                                {{ $mis->owner_name ?? '' }}
                                            </td>

                                            <td>
                                                {{ $mis->old_door_no ?? '' }}
                                            </td>

                                            <td>
                                                ₹{{ number_format($mis->balance ?? 0, 2) }}
                                            </td>

                                        </tr>

                                        @empty

                                        <tr>
                                            <td colspan="4" class="text-center">
                                                No MIS Data
                                            </td>
                                        </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection
