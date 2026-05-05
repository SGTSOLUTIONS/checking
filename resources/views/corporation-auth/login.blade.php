@extends('layouts.corporation-auth-layout')

@section('title', 'Corporation Login')

@section('content')
<div class="auth-card">
    <div class="login-hero">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-building"></i></div>
            <div>
                <div class="brand-text">Corporation Portal</div>
                <div class="brand-sub">Property Tax Management System</div>
            </div>
        </div>
        <div class="hero-content">
            <h1>Welcome<br><span class="hero-highlight">Back!</span></h1>
            <p class="hero-description">Sign in to access your corporation dashboard and manage property tax records.</p>
        </div>
    </div>

    <div class="login-form-section">
        <div class="mobile-header">
            <div class="brand-icon mx-auto"><i class="fas fa-building"></i></div>
            <div class="brand-text">Corporation Portal</div>
        </div>

        <div class="form-header">
            <h2>Sign In</h2>
            <p>Enter your credentials to access your account</p>
        </div>

        <form id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="input-label">Email Address</label>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>
                <div class="invalid-feedback" id="email_error"></div>
            </div>

            <div class="mb-3">
                <label class="input-label">Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="invalid-feedback" id="password_error"></div>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" name="remember"> <span>Remember me</span>
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
            Don't have an account? <a href="{{ route('corporation.register') }}">Register here</a>
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

        if (isSubmitting) return false;

        $('.invalid-feedback').text('');
        $('.input-field input').removeClass('is-invalid');

        const formData = {
            email: $('input[name="email"]').val(),
            password: $('input[name="password"]').val(),
            remember: $('input[name="remember"]').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!formData.email) {
            $('input[name="email"]').addClass('is-invalid');
            $('#email_error').text('Email is required.');
            showToast('error', 'Error', 'Please enter your email.', 3000);
            return false;
        }

        if (!formData.password) {
            $('input[name="password"]').addClass('is-invalid');
            $('#password_error').text('Password is required.');
            showToast('error', 'Error', 'Please enter your password.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#btnText').text('Signing in...');
        $('#btnSpinner').removeClass('d-none');
        $('#loginBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.login.submit') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Success!', response.message, 1500);
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#loginBtn').prop('disabled', false);
                $('#btnText').text('Sign In');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 401) {
                    $('input[name="password"]').addClass('is-invalid');
                    $('#password_error').text(xhr.responseJSON?.message);
                } else if (xhr.status === 404) {
                    $('input[name="email"]').addClass('is-invalid');
                    $('#email_error').text(xhr.responseJSON?.message);
                } else if (xhr.status === 403) {
                    showToast('error', 'Account Issue', xhr.responseJSON?.message, 4000);
                } else {
                    showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                }
            }
        });
    });
});
</script>
@endsection
