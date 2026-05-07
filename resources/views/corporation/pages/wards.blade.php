@extends('layouts.commissioner')

@section('title', 'Wards Management - Municipal Corporation')

@section('content')

<div class="animate__animated animate__fadeInUp">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h3 class="fw-bold" style="color:#ffffff;">
            <i class="fas fa-map-marker-alt me-2" style="color:#1679AB;"></i> Ward Management
        </h3>
        <input type="text" class="form-control w-25" placeholder="Search Ward by number..." id="searchWard">
    </div>

    <div class="row" id="wardsContainer">
        @foreach($wardData as $ward)
        <div class="col-xl-4 col-md-6 mb-4 ward-item" data-ward="{{ $ward->ward_no }}">
            <div class="ward-card">
                <div class="ward-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">Ward {{ $ward->ward_no }}</h4>
                            <p class="mb-0 opacity-75">Zone {{ $ward->zone }}</p>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-building fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="ward-stats">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="stat-box">
                                <i class="fas fa-building fa-2x mb-2" style="color:#1679AB;"></i>
                                <h4 class="fw-bold mb-0">{{ number_format($ward->buildingCount) }}</h4>
                                <p class="text-muted mb-0">Buildings</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <i class="fas fa-road fa-2x mb-2" style="color:#28a745;"></i>
                                <h4 class="fw-bold mb-0">-</h4>
                                <p class="text-muted mb-0">Roads</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('corporation.ward.details', $ward->ward_no) }}"
                       class="btn btn-primary w-100 py-2">
                        <i class="fas fa-map-marked-alt me-2"></i> View Ward Map
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.getElementById('searchWard')?.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const items = document.querySelectorAll('.ward-item');
        items.forEach(item => {
            const text = item.getAttribute('data-ward').toLowerCase();
            item.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

@endsection
