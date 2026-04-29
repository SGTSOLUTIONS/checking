{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth-layout')

@section('title', 'Login | TN Municipal Property Tax Portal')

@section('content')
<div class="auth-card">
    <!-- LEFT: Branding & municipal info -->
    <div class="login-hero">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-landmark"></i></div>
            <div>
                <div class="brand-text">Greater Chennai Corporation</div>
                <div class="brand-sub">Tamil Nadu • Tax Recognition Portal</div>
            </div>
        </div>
        <div class="hero-content">
            <h1>Property Tax<br><span class="hero-highlight">Digital Seva</span></h1>
            <p class="hero-description">
                Official portal for property tax assessment, online payment, and e-recognition certificates.
                Government of Tamil Nadu initiative for transparent governance.
            </p>
            <div class="trust-badge">
                <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>e-Governance</span></div>
                <div class="trust-item"><i class="fas fa-file-certificate"></i> <span>Tax Recognition</span></div>
                <div class="trust-item"><i class="fas fa-hand-holding-usd"></i> <span>Online Collection</span></div>
            </div>
        </div>
        <div class="quote-area">
            <div class="quote">“நேர்மையான வரி செலுத்துதல் - நகரத்தின் வளர்ச்சிக்கு” — Hon'ble Minister of Municipal Administration</div>
        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="login-form-section">
        <div class="form-header">
            <h2>Taxpayer Access</h2>
            <p>Sign in to view property tax, file returns & download e-receipts</p>
        </div>

        <form id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="input-label" for="email">Registered Mobile / Email (Tamil Nadu)</label>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="yourname@municipality.tn.gov.in" autocomplete="email" required>
                </div>
                <div class="invalid-feedback" id="email_error"></div>
            </div>

            <div class="mb-3">
                <label class="input-label" for="password">Tax Portal Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                </div>
                <div class="invalid-feedback" id="password_error"></div>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" id="rememberCheck" name="remember"> <span>Keep me signed in (secured device)</span>
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password / PID?</a>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <i class="fas fa-file-invoice-dollar"></i>
                <span id="btnText">Access Tax Dashboard</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <div class="divider"><span>OR SIGN IN WITH</span></div>
        <div class="sso-buttons">
            <button class="sso-btn" id="ssoGoogleBtn"><i class="fab fa-google"></i> TN e-Seva</button>
            <button class="sso-btn" id="ssoOktaBtn"><i class="fas fa-building"></i> UMANG SSO</button>
            <button class="sso-btn" id="ssoMsftBtn"><i class="fas fa-database"></i> DigiLocker</button>
        </div>

        <div class="register-prompt">
            New taxpayer? <a href="{{ route('register') }}">Register your property for tax recognition</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        // Reset validation
        $('.invalid-feedback').text('');
        $('.input-field input').removeClass('is-invalid');

        // Show loading state
        $('#btnText').text('Authenticating...');
        $('#btnSpinner').removeClass('d-none');
        $('#loginBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('login.post') }}",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(res) {
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Access Tax Dashboard');
                $('#btnSpinner').addClass('d-none');

                if (res.status === 'success') {
                    showToast('success', 'Welcome!', res.message, 2000);
                    setTimeout(function() {
                        window.location.href = res.redirect;
                    }, 1500);
                } else {
                    showToast('error', 'Login Failed', res.message, 4000);
                }
            },
            error: function(xhr) {
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Access Tax Dashboard');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error', 'Please check the form for errors.', 4000);
                } else if (xhr.status === 401 || xhr.status === 403 || xhr.status === 404) {
                    let message = xhr.responseJSON?.message || 'Invalid credentials or account issue.';
                    showToast('error', 'Authentication Failed', message, 4000);
                    if (xhr.status === 401) {
                        $('#password').addClass('is-invalid');
                        $('#password_error').text('Invalid password. Please try again.');
                    } else if (xhr.status === 404) {
                        $('#email').addClass('is-invalid');
                        $('#email_error').text('No account found with this email.');
                    }
                } else {
                    showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                }
            }
        });
    });

    // SSO Demo Buttons
    $('#ssoGoogleBtn').click(function() {
        showToast('info', 'TN e-Seva', 'Redirecting to Tamil Nadu Single Sign-On...', 2500);
        setTimeout(() => showToast('success', 'SSO Connected', 'Taxpayer records fetched', 2000), 1500);
    });

    $('#ssoOktaBtn').click(function() {
        showToast('info', 'UMANG Platform', 'Connecting to Unified Mobile App...', 2500);
        setTimeout(() => showToast('success', 'Verified', 'Property tax summary available', 2000), 1500);
    });

    $('#ssoMsftBtn').click(function() {
        showToast('info', 'DigiLocker', 'Authenticating via DigiLocker...', 2500);
        setTimeout(() => showToast('success', 'Authorized', 'Tax certificates accessible', 2000), 1500);
    });

    // Demo credentials double-click (for testing)
    $('.login-form-section').on('dblclick', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
        $('#email').val('taxpayer@tn.gov.in');
        $('#password').val('password123');
        showToast('info', 'Demo Credentials', 'Sample taxpayer account loaded', 2000);
    });

    // Welcome message
    showToast('info', '📜 Tamil Nadu Municipal Tax', 'Welcome to property tax e-portal | Secure GSTN integration', 4000);
});
</script>
@endsection
