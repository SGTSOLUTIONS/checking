@extends('layouts.authLayout')

@section('title', 'Surveyor Login | Spatial Revenue Intelligent System')

@section('content')
<div class="auth-card">
    <!-- LEFT: Branding & Surveyor Info -->
    <div class="login-hero">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-map-marked-alt"></i></div>
            <div>
                <div class="brand-text">Spatial Revenue Intelligent System</div>
                <div class="brand-sub">Surveyor Dashboard Access</div>
            </div>
        </div>

        <div class="hero-content">
            <h1>GIS Property<br><span class="hero-highlight">Survey Portal</span></h1>

            <p class="hero-description">
                Official platform for field surveyors to manage property inspections,
                GIS mapping, spatial data collection, tax verification, and building assessments
                across municipal regions.
            </p>

            <div class="trust-badge">
                <div class="trust-item">
                    <i class="fas fa-map"></i>
                    <span>GIS Mapping</span>
                </div>

                <div class="trust-item">
                    <i class="fas fa-building"></i>
                    <span>Property Assessment</span>
                </div>

                <div class="trust-item">
                    <i class="fas fa-database"></i>
                    <span>Spatial Data Collection</span>
                </div>
            </div>
        </div>

        <div class="quote-area">
            <div class="quote">
                “Accurate spatial surveying ensures transparent urban governance and smart taxation.”
            </div>
        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="login-form-section">
        <div class="form-header">
            <h2>Surveyor Login</h2>
            <p>Sign in to access survey tasks, GIS layers & property inspection records</p>
        </div>

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="input-label" for="email">
                    Official Email / Surveyor ID
                </label>

                <div class="input-field">
                    <i class="fas fa-user-tie"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="surveyor@sris.gov.in"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="invalid-feedback" id="email_error"></div>
            </div>

            <div class="mb-3">
                <label class="input-label" for="password">
                    Secure Password
                </label>

                <div class="input-field">
                    <i class="fas fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="invalid-feedback" id="password_error"></div>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" id="rememberCheck" name="remember">
                    <span>Keep me signed in</span>
                </label>

                <a href="{{ route('password.request') }}" class="forgot-link">
                    Forgot password?
                </a>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i>

                <span id="btnText">
                    Access Surveyor Dashboard
                </span>

                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <div class="divider">
            <span>OR CONTINUE WITH</span>
        </div>


        <div class="register-prompt">
            Need access permission?
            <a href="#">
                Contact System Administrator
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    let isSubmitting = false;

    $('#loginForm').on('submit', function(e) {

        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) {
            return false;
        }

        $('.invalid-feedback').text('');
        $('.input-field input').removeClass('is-invalid');

        const formData = {
            email: $('#email').val(),
            password: $('#password').val(),
            remember: $('#rememberCheck').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Validation
        if (!formData.email) {

            $('#email').addClass('is-invalid');
            $('#email_error').text('Official email is required.');

            showToast(
                'error',
                'Validation Error',
                'Please enter your official email.',
                3000
            );

            return false;
        }

        if (!formData.password) {

            $('#password').addClass('is-invalid');
            $('#password_error').text('Password is required.');

            showToast(
                'error',
                'Validation Error',
                'Please enter your password.',
                3000
            );

            return false;
        }

        // Loading State
        isSubmitting = true;

        $('#btnText').text('Authenticating...');
        $('#btnSpinner').removeClass('d-none');

        $('#loginBtn').prop('disabled', true);

        // AJAX Login
        $.ajax({

            url: "{{ route('login') }}",
            method: "POST",
            data: formData,
            dataType: "json",

            success: function(response) {

                isSubmitting = false;

                $('#loginBtn').prop('disabled', false);

                $('#btnText').text('Access Surveyor Dashboard');
                $('#btnSpinner').addClass('d-none');

                if (response.status === 'success') {

                    showToast(
                        'success',
                        'Welcome Surveyor',
                        response.message,
                        2000
                    );

                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);

                } else {

                    showToast(
                        'error',
                        'Login Failed',
                        response.message || 'Invalid login credentials.',
                        4000
                    );
                }
            },

            error: function(xhr) {

                isSubmitting = false;

                $('#loginBtn').prop('disabled', false);

                $('#btnText').text('Access Surveyor Dashboard');
                $('#btnSpinner').addClass('d-none');

                const message =
                    xhr.responseJSON?.message ||
                    'Something went wrong. Please try again later.';

                showToast(
                    'error',
                    'Authentication Failed',
                    message,
                    4000
                );
            }
        });

        return false;
    });

    // SSO Buttons
    $('#ssoGoogleBtn, #ssoOktaBtn, #ssoMsftBtn').click(function(e) {

        e.preventDefault();

        const ssoName = $(this).text().trim();

        showToast(
            'info',
            ssoName,
            'Redirecting to authentication service...',
            2500
        );
    });

    // Demo Login
    $('.login-form-section').on('dblclick', function(e) {

        if (
            e.target.tagName === 'INPUT' ||
            e.target.tagName === 'BUTTON'
        ) return;

        $('#email').val('surveyor@sris.gov.in');
        $('#password').val('password123');

        showToast(
            'info',
            'Demo Credentials',
            'Sample surveyor account loaded',
            2000
        );
    });

    // Clear validation
    $('#email, #password').on('input', function() {

        $(this).removeClass('is-invalid');

        $(this)
            .siblings('.invalid-feedback')
            .text('');
    });

    // Welcome Message
    setTimeout(() => {

        showToast(
            'info',
            '🗺️ SRIS Survey Dashboard',
            'Welcome to GIS property survey portal',
            4000
        );

    }, 500);

});
</script>
@endsection
