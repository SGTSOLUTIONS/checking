@extends('layouts.corporation-auth-layout')

@section('title', 'Login | Corporation Portal')

@section('content')
<div class="auth-card">
    <!-- LEFT: Branding -->
    <div class="login-hero">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-building"></i></div>
            <div>
                <div class="brand-text">Corporation Portal</div>
                <div class="brand-sub">Tamil Nadu • Property Tax System</div>
            </div>
        </div>
        <div class="hero-content">
            <h1>Property Tax<br><span class="hero-highlight">Management System</span></h1>
            <p class="hero-description">
                Official portal for corporation property tax assessment, collection, and management.
                Streamlined workflow for efficient governance.
            </p>
            <div class="trust-badge">
                <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>Secure Access</span></div>
                <div class="trust-item"><i class="fas fa-chart-line"></i> <span>Real-time Analytics</span></div>
                <div class="trust-item"><i class="fas fa-file-invoice"></i> <span>Digital Records</span></div>
            </div>
        </div>
        <div class="quote-area">
            <div class="quote">"Transparent Taxation - Building Better Cities" — Corporation Commissioner</div>
        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="login-form-section">
        <div class="mobile-header">
            <div class="brand-icon mx-auto"><i class="fas fa-building"></i></div>
            <div class="brand-text">Corporation Portal</div>
            <div class="brand-sub">Property Tax Management System</div>
        </div>

        <div class="form-header">
            <h2>Welcome Back</h2>
            <p>Sign in to access your corporation dashboard</p>
        </div>

        <form id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="input-label" for="email">Email Address</label>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="your@email.com" autocomplete="email" required>
                </div>
                <div class="invalid-feedback" id="email_error"></div>
            </div>

            <div class="mb-3">
                <label class="input-label" for="password">Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                </div>
                <div class="invalid-feedback" id="password_error"></div>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" id="rememberCheck" name="remember"> <span>Remember me</span>
                </label>
                <a href="{{ route('corporation.password.request') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i>
                <span id="btnText">Sign In</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <div class="register-prompt">
            Don't have an account? <a href="{{ route('corporation.register') }}">Register as Corporation User</a>
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

        if (isSubmitting) return false;

        // Reset validation
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

        $.ajax({
            url: "{{ route('corporation.login.submit') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                isSubmitting = false;
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Sign In');
                $('#btnSpinner').addClass('d-none');

                if (response.status === 'success') {
                    showToast('success', 'Welcome!', response.message, 2000);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Sign In');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}_error`).text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error', 'Please check the form for errors.', 4000);
                } else if (xhr.status === 401) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text(xhr.responseJSON?.message || 'Invalid password.');
                    showToast('error', 'Authentication Failed', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 404) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text(xhr.responseJSON?.message || 'Account not found.');
                    showToast('error', 'Account Not Found', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 403) {
                    showToast('error', 'Account Inactive', xhr.responseJSON?.message, 4000);
                } else {
                    showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                }
            }
        });

        return false;
    });

    // Clear validation on input
    $('#email, #password').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });

    // Welcome message
    setTimeout(() => {
        showToast('info', 'Corporation Portal', 'Welcome to Property Tax Management System', 4000);
    }, 500);
});
</script>
@endsection
