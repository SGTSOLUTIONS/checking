@extends('layouts.commissioner')

@section('title', 'Commissioner Dashboard')

@section('content')

<div class="container-fluid py-4">
    <!-- HEADER -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="dashboard-icon me-3">
                        <i class="fas fa-city"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold text-dark mb-1">Commissioner Dashboard</h2>
                        <p class="text-muted mb-0">
                            Welcome back,
                            <strong class="text-primary">{{ $corporation->corporation_name ?? 'N/A' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <button class="btn btn-primary px-4 py-2 shadow-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-2"></i>
                    Refresh Dashboard
                </button>
            </div>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="summary-card bg-primary">
                <div class="summary-icon">
                    <i class="fas fa-map"></i>
                </div>
                <div class="summary-content">
                    <h6>Total Wards</h6>
                    <h2>{{ number_format($ward_count) }}</h2>
                    <small>Active Wards</small>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="summary-card bg-success">
                <div class="summary-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="summary-content">
                    <h6>Total MIS Records</h6>
                    <h2>{{ number_format($mis_count) }}</h2>
                    <small>Property Tax Records</small>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 mb-4">
            <div class="summary-card bg-dark">
                <div class="summary-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="summary-content">
                    <h6>Total Collections</h6>
                    <h2>{{ number_format(count($collections)) }}</h2>
                    <small>Zone & Ward Collections</small>
                </div>
            </div>
        </div>
    </div>

    <!-- WARD CARDS -->
    <div class="row">
        @foreach ($collections as $collection)
            <div class="col-xl-6 mb-4">
                <div class="card ward-card border-0">
                    <!-- CARD HEADER -->
                    <div class="ward-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="zone-badge me-2">
                                        <i class="fas fa-location-dot"></i>
                                    </div>
                                    <h5 class="mb-0 fw-bold">Zone {{ $collection['zone'] }}</h5>
                                </div>
                                <p class="mb-0 opacity-75">
                                    Ward No : <strong>{{ $collection['ward_no'] }}</strong>
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="ward-number">WARD {{ $collection['ward_no'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD BODY -->
                    <div class="card-body p-4">
                        <!-- STATS -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="mini-stat">
                                    <div class="mini-icon bg-primary-subtle text-primary">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <h4>{{ number_format($collection['buildingCount']) }}</h4>
                                        <p>Total Buildings</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mini-stat">
                                    <div class="mini-icon bg-success-subtle text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h4>{{ number_format($collection['surveyedBuildingCount']) }}</h4>
                                        <p>Surveyed</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mini-stat">
                                    <div class="mini-icon bg-info-subtle text-info">
                                        <i class="fas fa-map-pin"></i>
                                    </div>
                                    <div>
                                        <h4>{{ number_format($collection['pointCount']) }}</h4>
                                        <p>Points</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mini-stat">
                                    <div class="mini-icon bg-danger-subtle text-danger">
                                        <i class="fas fa-road"></i>
                                    </div>
                                    <div>
                                        <h4>{{ number_format($collection['roadCount']) }}</h4>
                                        <p>Roads</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTON - Now navigates to ward details page -->
                        <div class="text-center">
                            <a href="{{ route('corporation.commissioner.ward.details', $collection['ward_no']) }}"
                               class="btn btn-outline-primary px-4">
                                <i class="fas fa-eye me-2"></i>
                                View on Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    body {
        background: #f4f7fb;
    }

    .dashboard-header {
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .dashboard-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        background: linear-gradient(135deg, #0d6efd, #0056d2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
    }

    .summary-card {
        border-radius: 20px;
        padding: 30px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .summary-card::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        top: -30px;
        right: -30px;
    }

    .summary-icon {
        font-size: 40px;
        margin-bottom: 20px;
    }

    .summary-content h2 {
        font-size: 38px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .ward-card {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06);
        transition: 0.3s ease;
    }

    .ward-card:hover {
        transform: translateY(-5px);
    }

    .ward-header {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: white;
        padding: 22px 25px;
    }

    .zone-badge {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ward-number {
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .mini-stat {
        background: #f8fafc;
        border-radius: 18px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: 0.3s;
    }

    .mini-stat:hover {
        background: white;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .mini-icon {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .mini-stat h4 {
        margin: 0;
        font-weight: 700;
    }

    .mini-stat p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .btn {
        border-radius: 12px;
        font-weight: 600;
    }

    @media(max-width:768px) {
        .summary-content h2 {
            font-size: 28px;
        }
        .ward-header {
            padding: 18px;
        }
    }
</style>

@endsection
