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

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="input-label" for="email">Registered Mobile / Email (Tamil Nadu)</label>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="yourname@municipality.tn.gov.in" autocomplete="email" required>
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
            <button type="button" class="sso-btn" id="ssoGoogleBtn"><i class="fab fa-google"></i> TN e-Seva</button>
            <button type="button" class="sso-btn" id="ssoOktaBtn"><i class="fas fa-building"></i> UMANG SSO</button>
            <button type="button" class="sso-btn" id="ssoMsftBtn"><i class="fas fa-database"></i> DigiLocker</button>
        </div>

        <div class="register-prompt">
            New taxpayer? <a href="{{ route('register') }}">Register your property for tax recognition</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let isSubmitting = false;

    // Handle form submission with AJAX
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) {
            return false;
        }

        // Reset validation errors
        $('.invalid-feedback').text('');
        $('.input-field input').removeClass('is-invalid');

        const formData = {
            email: $('#email').val(),
            password: $('#password').val(),
            remember: $('#rememberCheck').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Client-side validation
        if (!formData.email) {
            $('#email').addClass('is-invalid');
            $('#email_error').text('Email address is required.');
            showToast('error', 'Validation Error', 'Please enter your email address.', 3000);
            return false;
        }

        if (!formData.password) {
            $('#password').addClass('is-invalid');
            $('#password_error').text('Password is required.');
            showToast('error', 'Validation Error', 'Please enter your password.', 3000);
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            $('#email').addClass('is-invalid');
            $('#email_error').text('Please enter a valid email address.');
            showToast('error', 'Validation Error', 'Please enter a valid email address.', 3000);
            return false;
        }

        // Show loading state
        isSubmitting = true;
        $('#btnText').text('Authenticating...');
        $('#btnSpinner').removeClass('d-none');
        $('#loginBtn').prop('disabled', true);

        // AJAX request
        $.ajax({
            url: "{{ route('login') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                isSubmitting = false;
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Access Tax Dashboard');
                $('#btnSpinner').addClass('d-none');

                if (response.status === 'success') {
                    showToast('success', 'Welcome!', response.message, 2000);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    showToast('error', 'Login Failed', response.message || 'Invalid credentials. Please try again.', 4000);

                    if (response.message && response.message.toLowerCase().includes('password')) {
                        $('#password').addClass('is-invalid');
                        $('#password_error').text(response.message);
                    } else if (response.message && response.message.toLowerCase().includes('email')) {
                        $('#email').addClass('is-invalid');
                        $('#email_error').text(response.message);
                    }
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Access Tax Dashboard');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}_error`).text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error', 'Please check the form for errors.', 4000);
                } else if (xhr.status === 401) {
                    const message = xhr.responseJSON?.message || 'Invalid password. Please try again.';
                    $('#password').addClass('is-invalid');
                    $('#password_error').text(message);
                    showToast('error', 'Authentication Failed', message, 4000);
                } else if (xhr.status === 404) {
                    const message = xhr.responseJSON?.message || 'No account found with this email address.';
                    $('#email').addClass('is-invalid');
                    $('#email_error').text(message);
                    showToast('error', 'Account Not Found', message, 4000);
                } else {
                    const message = xhr.responseJSON?.message || 'Something went wrong. Please try again later.';
                    showToast('error', 'Error', message, 4000);
                }
            }
        });

        return false;
    });

    // SSO Buttons
    $('#ssoGoogleBtn, #ssoOktaBtn, #ssoMsftBtn').click(function(e) {
        e.preventDefault();
        const ssoName = $(this).text().trim();
        showToast('info', `${ssoName}`, 'Redirecting to SSO provider...', 2500);
        setTimeout(() => {
            showToast('success', 'SSO Connected', 'Authentication successful', 2000);
        }, 1500);
    });

    // Demo credentials on double click
    $('.login-form-section').on('dblclick', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
        $('#email').val('taxpayer@tn.gov.in');
        $('#password').val('password123');
        showToast('info', 'Demo Credentials', 'Sample taxpayer account loaded', 2000);
    });

    // Clear validation on input
    $('#email, #password').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });

    // Welcome message
    setTimeout(() => {
        showToast('info', '📜 Tamil Nadu Municipal Tax', 'Welcome to property tax e-portal', 4000);
    }, 500);
});
</script>
@endsection
