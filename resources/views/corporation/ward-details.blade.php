@extends('layouts.commissioner')

@section('title', 'Hyperlocal Ward View')

@section('content-panels')
    <div class="content-panel">
        <div class="mb-4"><a href="{{ route('corporation.dashboard') }}" class="btn btn-outline-secondary rounded-pill"><i
                    class="fas fa-chevron-left me-2"></i>Back to Dashboard</a></div>
        <div class="stat-card p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold">Ward {{ $ward->ward_no }} · <span
                            class="text-teal">{{ $ward->zone ?? 'Central Zone' }}</span></h2>
                    <p class="text-secondary">Comprehensive GIS asset registry and infrastructure summary.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="bg-light rounded-3 p-3 d-inline-block"><span
                            class="fw-bold fs-3">{{ $totalBuildings ?? 0 }}</span> <span class="text-muted">Total
                            Assets</span></div>
                </div>
            </div>
            <hr>
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="border rounded-4 p-3 text-center"><i class="fas fa-draw-polygon fa-2x text-teal mb-2"></i>
                        <h4 class="fw-bold">{{ $gisIdCount ?? 0 }}</h4><small>GIS Tagged Properties</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-4 p-3 text-center"><i class="fas fa-road fa-2x text-teal mb-2"></i>
                        <h4 class="fw-bold">{{ $totalRoads ?? 0 }}</h4><small>Road segments (km)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-4 p-3 text-center"><i class="fas fa-map-pin fa-2x text-teal mb-2"></i>
                        <h4 class="fw-bold">{{ $totalPoints ?? 0 }}</h4><small>Point of Interest</small>
                    </div>
                </div>
            </div>
            <div class="mt-4 p-3 bg-light rounded-4 text-center text-muted"><i class="fas fa-map me-2"></i> Interactive map
                module ready · <strong>{{ count($polygons ?? []) }} buildings, {{ count($roads ?? []) }} roads</strong> —
                full vector tiles integration pending official GIS layer.</div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="fw-semibold mb-2"><i class="fas fa-building me-2"></i>Building extract</div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>GIS ID</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($polygons ?? []) as $pg)
                                    <tr>
                                        <td>{{ $pg->gisid ?? '—' }}</td>
                                        <td>{{ $pg->owner_name ?? 'NA' }}</td>
                                        <td><span class="badge bg-success">Mapped</span></td>
                                </tr>@empty <tr>
                                        <td colspan="3" class="text-center">Pending upload</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fw-semibold mb-2"><i class="fas fa-road me-2"></i>Road network inventory</div>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Road name</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($roads ?? []) as $rd)
                                <tr>
                                    <td>{{ $rd->road_name ?? 'Unnamed' }}</td>
                                    <td>Arterial</td>
                            </tr>@empty <tr>
                                    <td colspan="2" class="text-center">No road data present</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
