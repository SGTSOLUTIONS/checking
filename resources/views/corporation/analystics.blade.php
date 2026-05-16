{{-- resources/views/corporation/analystics.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Analystics - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

@section('content')

    <div class="dashboard-content-area">

        <div class="animate__animated animate__fadeInUp">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h3 class="fw-bold text-white">
                    <i class="fas fa-tachometer-alt me-2" style="color:#1679AB;"></i>
                    Analystics Overview - {{ $corporation->name ?? '' }}
                </h3>
                <div>
                    <span class="badge bg-light text-dark p-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ now()->format('d M Y') }}
                    </span>
                </div>
            </div>

            <!-- Statistics Cards - Row 1 -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Buildings</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_building) }}</h2>
                            <small class="text-success"><i class="fas fa-building"></i> Polygon records</small>
                        </div>
                        <div class="stat-icon"><i class="fas fa-building"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Surveyed Buildings</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_surveyed_building) }}
                            </h2>
                            <small class="text-info"><i class="fas fa-check-circle"></i> {{ $survey_percentage }}%
                                coverage</small>
                        </div>
                        <div class="stat-icon bg-info-subtle"><i class="fas fa-clipboard-list text-info"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Surveyed Assessments</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_surveyed_assessment) }}
                            </h2>
                            <small class="text-warning"><i class="fas fa-file-alt"></i> Point data records</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle"><i class="fas fa-chart-line text-warning"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">MIS Records</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_mis) }}</h2>
                            <small class="text-danger"><i class="fas fa-database"></i> Total entries</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle"><i class="fas fa-database text-danger"></i></div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards - Row 2 -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Wards</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ $ward_count }}</h2>
                            <small class="text-success"><i class="fas fa-map-marked-alt"></i> Active wards</small>
                        </div>
                        <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Shops</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shops) }}</h2>
                            <small class="text-primary"><i class="fas fa-store"></i> Registered shops</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle"><i class="fas fa-store text-primary"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Shop Data in MIS</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($total_shop_data_in_mis) }}
                            </h2>
                            <small class="text-success"><i class="fas fa-link"></i> Matched records</small>
                        </div>
                        <div class="stat-icon bg-success-subtle"><i class="fas fa-check-double text-success"></i></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Shop Data Not in MIS</h6>
                            <h2 class="fw-bold mb-0" style="color:#102C57;">
                                {{ number_format($total_shop_data_not_in_mis) }}</h2>
                            <small class="text-danger"><i class="fas fa-unlink"></i> Unmatched records</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle"><i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Ward Cards Section -->
            <div class="row">
                <div class="col-12">
                    <div class="stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                            <h4 class="fw-bold mb-0">
                                <i class="fas fa-building me-2" style="color:#1679AB;"></i>
                                Ward-wise Detailed Information
                            </h4>
                            {{-- <div class="mt-2 mt-sm-0">
                                <span class="badge bg-info">
                                    <i class="fas fa-layer-group"></i> Showing {{ $wards_pagination->firstItem() }} to
                                    {{ $wards_pagination->lastItem() }} of {{ $wards_pagination->total() }} wards
                                </span>
                            </div> --}}
                        </div>



                        <!-- Ward Cards Grid -->
                        <div class="row g-4" id="wardsContainer">

                        </div>

                        <!-- Pagination -->
                        @if ($wards_pagination->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                                <div class="mb-2 mb-sm-0">
                                    <small class="text-muted">
                                        Showing {{ $wards_pagination->firstItem() ?? 0 }} to
                                        {{ $wards_pagination->lastItem() ?? 0 }} of {{ $wards_pagination->total() ?? 0 }}
                                        wards
                                    </small>
                                </div>
                                <div>
                                    {{ $wards_pagination->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            const corporation = @json($corporation);
            const ward_count = @json($ward_count);
            const total_building = @json($total_building);
            const total_surveyed_building = @json($total_surveyed_building);
            const total_surveyed_assessment = @json($total_surveyed_assessment);
            const total_mis = @json($total_mis);
            const total_shops = @json($total_shops);
            const total_shop_data_count = @json($total_shop_data_count);
            const total_shop_data_in_mis = @json($total_shop_data_in_mis);
            const total_shop_data_not_in_mis = @json($total_shop_data_not_in_mis);
            const survey_percentage = @json($survey_percentage);
            const wards = @json($wards);
            const wards_per_zones = @json($wards_per_zones);

            const $wardcontainer = $("#wardsContainer");

            renderWards(wards);

            function renderWards(wards) {

                $wardcontainer.empty();

                wards.forEach(function(ward) {

                    let card = `
                    <div class="card p-3 mb-2">
                        <h2>${ward.id}</h2>
                    </div>
                `;

                    $wardcontainer.append(card);

                });
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

        /* Ward Card Styles */
        .ward-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            opacity: 0;
        }

        .ward-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .ward-card-header {
            color: white;
        }

        .ward-card-body {
            background: white;
        }

        .ward-card-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-section {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding-bottom: 10px;
        }

        .info-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-item {
            transition: all 0.2s ease;
        }

        .info-item:hover {
            transform: scale(1.02);
            background: #e9ecef !important;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 20px;
        }

        /* Button Styles */
        .btn-sm {
            border-radius: 8px;
            padding: 5px 12px;
        }

        .btn-outline-primary:hover {
            background: #1679AB;
            border-color: #1679AB;
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

        .input-group-text {
            border-radius: 10px 0 0 10px;
        }

        /* Pagination Styles */
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

        /* Responsive Design */
        @media(max-width: 1200px) {
            .ward-card-header h5 {
                font-size: 1.1rem;
            }

            .info-item strong {
                font-size: 1.2rem;
            }
        }

        @media(max-width: 768px) {
            .dashboard-content-area {
                padding: 15px;
            }

            .stat-card {
                margin-bottom: 15px;
            }

            .ward-card {
                margin-bottom: 15px;
            }

            .info-item strong {
                font-size: 1rem;
            }

            .btn-sm {
                padding: 4px 10px;
                font-size: 11px;
            }

            .badge {
                font-size: 10px;
            }
        }

        @media(max-width: 576px) {
            .ward-card-header {
                flex-direction: column;
                gap: 10px;
            }

            .ward-card-header .btn {
                width: 100%;
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

        /* Hover Effects */
        .ward-card .btn {
            transition: all 0.2s ease;
        }

        .ward-card .btn:hover {
            transform: translateX(3px);
        }

        /* Statistics Card Icons */
        .stat-icon i {
            font-size: 28px;
        }

        /* Progress Bar Colors */
        .progress-bar.bg-info {
            background-color: #1679AB !important;
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
    </style>
@endpush
