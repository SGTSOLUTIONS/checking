{{-- resources/views/layouts/commissioner.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($corporation->name ?? 'Tamil Nadu Municipal Corporation') . ' | Admin Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .corporation-logo, .corporation-logo-large {
            background: white;
            border-radius: 50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
        }
        .corporation-logo { width:80px; height:80px; }
        .corporation-logo-large { width:120px; height:120px; font-size:40px; }
        .corporation-profile-icon {
            width:40px;height:40px;border-radius:50%;object-fit:cover;
        }
        .user-avatar {
            width:42px;height:42px;background:#1679AB;border-radius:50%;
            display:flex;align-items:center;justify-content:center;color:#fff;
        }
    </style>
</head>

<body>

@php
    // SIDEBAR LOGO
    $sidebarLogoUrl = null;
    if (!empty($corporation->logo)) {
        $path = public_path($corporation->logo);
        if (file_exists($path)) {
            $sidebarLogoUrl = asset($corporation->logo);
        }
    }

    // NAVBAR AVATAR LOGO
    $avatarLogoUrl = null;
    if (!empty($corporation->logo)) {
        $path = storage_path('app/public/' . $corporation->logo);
        if (file_exists($path)) {
            $avatarLogoUrl = asset('storage/' . $corporation->logo);
        }
    }

    // MODAL LOGO
    $modalLogoUrl = null;
    if (!empty($corporation->logo)) {
        $path = storage_path('app/public/' . $corporation->logo);
        if (file_exists($path)) {
            $modalLogoUrl = asset('storage/' . $corporation->logo);
        }
    }

    $initials = strtoupper(substr($corporation->name ?? 'TN', 0, 2));
@endphp


<div class="container-fluid p-0">
    <div class="row g-0">

        <!-- SIDEBAR -->
        <div class="col-auto sidebar min-vh-100">

            <div class="text-center p-3 border-bottom">

                @if($sidebarLogoUrl)
                    <img src="{{ $sidebarLogoUrl }}" class="corporation-logo mb-2">
                @else
                    <div class="corporation-logo mb-2">
                        {{ $initials }}
                    </div>
                @endif

                <h6 class="text-white">{{ $corporation->name ?? 'TN Corp' }}</h6>
            </div>

        </div>

        <!-- MAIN -->
        <div class="col">

            <!-- NAVBAR -->
            <nav class="p-3 bg-white d-flex justify-content-between">

                <div></div>

                <div class="dropdown">

                    <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">

                        <div class="user-avatar">

                            @if($avatarLogoUrl)
                                <img src="{{ $avatarLogoUrl }}" class="corporation-profile-icon">
                            @else
                                <i class="fas fa-building"></i>
                            @endif

                        </div>

                        <div>
                            <div class="fw-bold">{{ $corporation->name ?? 'Admin' }}</div>
                            <small class="text-muted">Municipal</small>
                        </div>

                    </div>

                </div>

            </nav>

            <!-- CONTENT -->
            <div class="p-4">
                @yield('content')
            </div>

        </div>
    </div>
</div>


<!-- CORPORATION MODAL -->
<div class="modal fade" id="corporationProfileModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>Corporation Profile</h5>
            </div>

            <div class="modal-body text-center">

                @if($modalLogoUrl)
                    <img src="{{ $modalLogoUrl }}" class="corporation-logo-large mb-3">
                @else
                    <div class="corporation-logo-large mb-3">
                        {{ $initials }}
                    </div>
                @endif

                <h5>{{ $corporation->name ?? 'N/A' }}</h5>
                <p>{{ $corporation->district ?? '' }}</p>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
