@extends('layouts.corporation-auth')

@section('title', 'Login | Corporation Portal')

@section('form-content')
<div id="loginFormContainer" class="form-container fade-in">
    <div class="mb-4 text-center text-md-start">
        <h4 class="fw-bold" style="color: #1a3c5c;">Welcome to Corporation e-Services</h4>
        <p class="text-secondary small">Sign in to access property tax, licenses, and management dashboard</p>
    </div>

    <form id="loginForm">
        @csrf
        <div class="mb-3">
            <label for="loginEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="loginEmail" name="email" placeholder="your@email.com" required>
            </div>
            <div class="invalid-feedback" id="emailError"></div>
        </div>

        <div class="mb-3">
            <label for="loginPassword" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="loginPassword" style="border-left:0;">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <div class="invalid-feedback" id="passwordError"></div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberCheck" name="remember" style="border-color:#2d6a4f;">
                <label class="form-check-label small" for="rememberCheck" style="color:#1a3c5c;">Remember me</label>
            </div>
            <a href="{{ route('corporation.password.request') }}" class="forgot-link"><i class="fas fa-question-circle"></i> Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="loginSubmitBtn">
            <i class="fas fa-arrow-right-to-bracket me-2"></i> Access Corporation Dashboard
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p class="signup-text" style="color:#5c6e7e;">Don't have a corporation account?
            <a href="{{ route('corporation.register') }}" class="text-decoration-none fw-bold" style="color:#2d6a4f;">Register as Corporation User</a>
        </p>
    </div>

    <div class="text-center mt-2">
        <small class="text-muted"><i class="fas fa-headset"></i> Corporation Helpline: 1913 | <i class="fas fa-envelope"></i> support@corporation.tn.gov.in</small>
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

        // Reset validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const email = $('#loginEmail').val().trim();
        const password = $('#loginPassword').val();
        const remember = $('#rememberCheck').is(':checked') ? 1 : 0;

        if (!email) {
            $('#loginEmail').addClass('is-invalid');
            $('#emailError').text('Email address is required.');
            showToast('error', 'Validation Error', 'Please enter your email address.', 3000);
            return false;
        }

        if (!password) {
            $('#loginPassword').addClass('is-invalid');
            $('#passwordError').text('Password is required.');
            showToast('error', 'Validation Error', 'Please enter your password.', 3000);
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('#loginEmail').addClass('is-invalid');
            $('#emailError').text('Please enter a valid email address.');
            showToast('error', 'Validation Error', 'Please enter a valid email address.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#loginSubmitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i> Authenticating...').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.login.submit') }}",
            method: "POST",
            data: {
                email: email,
                password: password,
                remember: remember,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Welcome!', response.message, 1500);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#loginSubmitBtn').html('<i class="fas fa-arrow-right-to-bracket me-2"></i> Access Corporation Dashboard').prop('disabled', false);

                if (xhr.status === 401) {
                    $('#loginPassword').addClass('is-invalid');
                    $('#passwordError').text(xhr.responseJSON?.message || 'Invalid password.');
                    showToast('error', 'Authentication Failed', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 404) {
                    $('#loginEmail').addClass('is-invalid');
                    $('#emailError').text(xhr.responseJSON?.message || 'Account not found.');
                    showToast('error', 'Account Not Found', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 403) {
                    showToast('error', 'Account Issue', xhr.responseJSON?.message, 4000);
                } else {
                    showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                }
            }
        });
    });

    // Clear validation on input
    $('#loginEmail, #loginPassword').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });
});
</script>
@endsection
