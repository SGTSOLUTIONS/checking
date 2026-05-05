@extends('layouts.corporation-auth-layout')

@section('title', 'Forgot Password | Corporation Portal')

@section('content')
<div class="auth-card" style="max-width: 600px;">
    <div class="login-form-section" style="width: 100%;">
        <div class="mobile-header">
            <div class="brand-icon mx-auto"><i class="fas fa-building"></i></div>
            <div class="brand-text">Corporation Portal</div>
            <div class="brand-sub">Property Tax Management System</div>
        </div>

        <div class="form-header text-center">
            <i class="fas fa-key fa-3x text-primary mb-3"></i>
            <h2>Forgot Password?</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <form id="forgotPasswordForm">
            @csrf
            <div class="mb-3">
                <label class="input-label">Email Address</label>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>
                <div class="invalid-feedback" id="email_error"></div>
            </div>

            <button type="submit" class="login-btn" id="resetBtn">
                <i class="fas fa-paper-plane"></i>
                <span id="btnText">Send Reset Link</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <div class="back-to-login mt-3">
            <a href="{{ route('corporation.login') }}"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let isSubmitting = false;

    $('#forgotPasswordForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) return false;

        $('.invalid-feedback').text('');
        $('.input-field input').removeClass('is-invalid');

        const email = $('input[name="email"]').val();

        if (!email) {
            $('input[name="email"]').addClass('is-invalid');
            $('#email_error').text('Email address is required.');
            showToast('error', 'Validation Error', 'Please enter your email address.', 3000);
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('input[name="email"]').addClass('is-invalid');
            $('#email_error').text('Please enter a valid email address.');
            showToast('error', 'Validation Error', 'Please enter a valid email address.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#btnText').text('Sending...');
        $('#btnSpinner').removeClass('d-none');
        $('#resetBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.password.email') }}",
            method: "POST",
            data: { email: email, _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Email Sent', response.message, 5000);
                    setTimeout(() => {
                        window.location.href = "{{ route('corporation.login') }}";
                    }, 3000);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#resetBtn').prop('disabled', false);
                $('#btnText').text('Send Reset Link');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    $('input[name="email"]').addClass('is-invalid');
                    $('#email_error').text(xhr.responseJSON?.message || 'Invalid email address.');
                    showToast('error', 'Error', xhr.responseJSON?.message, 4000);
                } else {
                    showToast('error', 'Error', xhr.responseJSON?.message || 'Something went wrong.', 4000);
                }
            }
        });
    });

    $('input[name="email"]').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#email_error').text('');
    });
});
</script>
@endsection
