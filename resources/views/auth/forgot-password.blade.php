@extends('layouts.authLayout')

@section('title', 'Forgot Password - Reset Your Account')

@section('content')
    <div class="form-header">
        <h2>Reset Your Password</h2>
        <p>We'll send you a secure link to reset your password</p>
    </div>

    <div id="alert-container"></div>

    <form id="forgotPasswordForm" method="POST">
        @csrf

        <div class="mb-4 text-center">
            <div style="background: #fef5e8; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-key fa-2x" style="color: #e67e22;"></i>
            </div>
            <p class="text-muted" style="font-size: 0.85rem;">
                Enter your registered email address and we'll send you a password reset link.
            </p>
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="input-label" for="email">
                <i class="fas fa-envelope me-2"></i>Registered Email Address
            </label>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" class=""
                       placeholder="yourname@municipality.tn.gov.in"
                       autocomplete="email" value="{{ old('email') }}">
            </div>
            <div class="invalid-feedback-custom" id="email_error"></div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="login-btn" id="resetBtn">
            <span id="btnText"><i class="fas fa-paper-plane"></i> Send Reset Link</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
        </button>

        <div class="register-prompt mt-4">
            <a href="{{ route('login') }}" class="forgot-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Login
            </a>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>Secured by Tamil Nadu e-Governance
            </small>
        </div>
    </form>
@endsection

@section('scripts')
<script>
$(function () {
    $('#forgotPasswordForm').on('submit', function (e) {
        e.preventDefault();

        // Clear previous errors and alerts
        $('#alert-container').html('');
        $('#email').removeClass('is-invalid');
        $('#email_error').text('');

        // Show loading state
        $('#btnText').html('<i class="fas fa-paper-plane"></i> Sending...');
        $('#btnSpinner').removeClass('d-none');
        $('#resetBtn').prop('disabled', true);

        const formData = {
            email: $('#email').val().trim(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "{{ route('password.email') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
                $('#resetBtn').prop('disabled', false);
                $('#btnText').html('<i class="fas fa-paper-plane"></i> Send Reset Link');
                $('#btnSpinner').addClass('d-none');

                if (res.status === 'success') {
                    showToast('success', 'Reset Link Sent!', res.message || 'Check your email for password reset instructions.', 5000);
                    // Optionally clear the form
                    $('#email').val('');

                    // Optional: Show additional info panel
                    setTimeout(() => {
                        showToast('info', 'Important Note', 'The reset link expires in 60 minutes.', 4000);
                    }, 3000);
                } else {
                    showToast('error', 'Error!', res.message || 'Something went wrong. Please try again.', 5000);
                }
            },
            error: function (xhr) {
                $('#resetBtn').prop('disabled', false);
                $('#btnText').html('<i class="fas fa-paper-plane"></i> Send Reset Link');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.email) {
                        $('#email').addClass('is-invalid');
                        $('#email_error').text(errors.email[0]);
                    }
                    showToast('error', 'Validation Error!', 'Please enter a valid email address.', 5000);
                } else if (xhr.status === 404) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text('No account found with this email address.');
                    showToast('error', 'Email Not Found!', 'No taxpayer account exists with this email.', 5000);
                } else if (xhr.status === 429) {
                    showToast('warning', 'Too Many Attempts', 'Please wait before trying again.', 5000);
                } else {
                    showToast('error', 'Error!', 'Unable to process your request. Please try again later.', 5000);
                }
            }
        });
    });

    // Clear validation on focus
    $('#email').on('focus', function() {
        $(this).removeClass('is-invalid');
        $('#email_error').text('');
    });

    // Optional: Demo mode - double click to populate demo email
    $('.login-form-section').on('dblclick', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
        $('#email').val('taxpayer@tn.gov.in');
        showToast("info", "Demo Email", "Demo email address loaded for testing", 2200);
    });
});
</script>
@endsection
