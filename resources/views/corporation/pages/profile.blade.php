@extends('layouts.commissioner')

@section('title', 'Profile - Municipal Corporation')

@section('content')

<div class="animate__animated animate__fadeInUp">
    <h3 class="fw-bold mb-4" style="color:#ffffff;">
        <i class="fas fa-user-circle me-2" style="color:#1679AB;"></i> My Profile
    </h3>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="stat-card p-4 text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 120px; height: 120px; font-size: 48px;">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="fw-bold">{{ $user->name ?? 'Commissioner' }}</h4>
                <p class="text-muted">Municipal Commissioner</p>
                <hr>
                <p><i class="fas fa-building me-2"></i> {{ $corporation->corporation_name ?? 'N/A' }}</p>
                <p><i class="fas fa-envelope me-2"></i> {{ $user->email ?? 'N/A' }}</p>
                <p><i class="fas fa-phone me-2"></i> {{ $user->phone ?? 'Not provided' }}</p>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="stat-card p-4">
                <h5 class="fw-bold mb-4">Update Profile Information</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('corporation.profile.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password (optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
